<?php
/**
 * One-off: end-to-end functional audit against the LIVE server, exactly as a
 * browser would behave (real cookies, real CSRF, real auth, real uploads).
 *
 * Usage: php tools/e2e-test.php
 */

const BASE = 'http://127.0.0.1:8000';

// Bootstrap Laravel so we can inspect the DB and clear throttles directly.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

$pass = 0;
$fail = 0;

function report(string $label, bool $ok, string $detail = ''): void
{
    global $pass, $fail;
    $ok ? $pass++ : $fail++;
    printf("[%s] %-52s %s\n", $ok ? 'PASS' : 'FAIL', $label, $detail);
}

/** Wipe throttles so repeated audit runs don't trip 429s. */
function clearThrottles(): void
{
    try {
        DB::table('cache')->delete();
    } catch (Throwable $e) {
        // cache table missing — nothing to clear
    }
}

/**
 * Minimal browser: cookie jar, CSRF, multipart uploads, and the choice of
 * whether to follow redirects (needed to observe 302s).
 */
class Browser
{
    public $ch;
    public string $jar;
    public ?string $lastBody = null;
    public bool $follow = true;

    public function __construct(string $name)
    {
        $this->jar = sys_get_temp_dir() . "/dsc-e2e-{$name}-" . getmypid() . '.cookies';
        @unlink($this->jar);
        $this->ch = curl_init();
    }

    private function base(): array
    {
        return [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => $this->follow,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_COOKIEJAR => $this->jar,
            CURLOPT_COOKIEFILE => $this->jar,
            CURLOPT_TIMEOUT => 45,
            CURLOPT_HEADER => false,
        ];
    }

    /** GET a page. $html=true sends a browser-like Accept header. */
    public function get(string $path, bool $html = true): array
    {
        curl_reset($this->ch);
        curl_setopt_array($this->ch, $this->base());
        curl_setopt($this->ch, CURLOPT_URL, BASE . $path);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
            'Accept: ' . ($html ? 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' : 'application/json'),
        ]);
        $out = curl_exec($this->ch);
        $this->lastBody = is_string($out) ? $out : '';

        return [(int) curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE), $this->lastBody];
    }

    /** POST urlencoded form data. */
    public function post(string $path, array $data, bool $expectJson = false): array
    {
        curl_reset($this->ch);
        curl_setopt_array($this->ch, $this->base());
        curl_setopt($this->ch, CURLOPT_URL, BASE . $path);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: ' . ($expectJson ? 'application/json' : 'text/html,application/xhtml+xml,*/*;q=0.8'),
        ]);
        $out = curl_exec($this->ch);
        $this->lastBody = is_string($out) ? $out : '';

        return [(int) curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE), $this->lastBody];
    }

    /**
     * POST multipart form data with file uploads (fields[key]=file).
     * $files maps nested field key => [tmpPath, filename, mime].
     *
     * NOTE: we hand-build the body rather than passing an array to
     * CURLOPT_POSTFIELDS — on this machine's PHP 8.5.10/curl, the array form
     * silently drops the `name=` attribute from each part, so the server sees
     * numeric keys (0, 1, 2...) and CSRF/field validation fail. Hand-building
     * matches what a real browser's FormData produces.
     */
    public function postMultipart(string $path, array $fields, array $files, bool $expectJson = true): array
    {
        $boundary = '----DSCAudit' . bin2hex(random_bytes(16));
        $body = '';

        $append = function (string $name, string $contents, ?string $filename = null, ?string $mime = null) use (&$body, $boundary): void {
            $body .= "--{$boundary}\r\n";
            $body .= 'Content-Disposition: form-data; name="' . $name . '"';
            if ($filename !== null) {
                $body .= '; filename="' . addslashes($filename) . '"';
            }
            $body .= "\r\n";
            if ($mime !== null) {
                $body .= "Content-Type: {$mime}\r\n";
            }
            $body .= "\r\n{$contents}\r\n";
        };

        foreach ($fields as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $sk => $sv) {
                    $append("{$k}[{$sk}]", (string) $sv);
                }
            } else {
                $append((string) $k, (string) $v);
            }
        }
        foreach ($files as $key => [$tmp, $filename, $mime]) {
            $append($key, (string) file_get_contents($tmp), $filename, $mime);
        }
        $body .= "--{$boundary}--\r\n";

        curl_reset($this->ch);
        curl_setopt_array($this->ch, $this->base());
        curl_setopt($this->ch, CURLOPT_URL, BASE . $path);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
            "Content-Type: multipart/form-data; boundary={$boundary}",
            'Accept: ' . ($expectJson ? 'application/json' : 'text/html,*/*;q=0.8'),
        ]);
        $out = curl_exec($this->ch);
        $this->lastBody = is_string($out) ? $out : '';

        return [(int) curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE), $this->lastBody];
    }

    /**
     * Return the PLAIN CSRF token from the last HTML page's hidden form input.
     * The XSRF-TOKEN cookie is Laravel's *encrypted* blob and is only accepted
     * via the X-XSRF-TOKEN header, never as the _token form field — so we must
     * read it from a rendered form, exactly like a browser does.
     */
    public function csrf(): string
    {
        if ($this->lastBody && preg_match('/name="_token"\s+value="([^"]+)"/', $this->lastBody, $m)) {
            return $m[1];
        }
        if ($this->lastBody && preg_match('/<meta name="csrf-token" content="([^"]+)"/', $this->lastBody, $m)) {
            return $m[1];
        }

        return '';
    }

    /** Refresh a form-bearing page and return a fresh plain token. */
    public function token(): string
    {
        $this->get('/contact');

        return $this->csrf();
    }

    public function __destruct()
    {
        @unlink($this->jar);
    }
}

echo "============================================================\n";
echo ' E2E FUNCTIONAL AUDIT — live server ' . BASE . "\n";
echo "============================================================\n\n";

clearThrottles();

$anon = new Browser('anon');
list($st) = $anon->get('/contact');
report('Public site reachable', $st === 200, "GET /contact -> $st");
$token = $anon->csrf();
report('CSRF token obtainable', $token !== '', strlen($token) . ' chars');

// ===============================================================
echo "--- 1. Service catalogue (client side) ---\n";

list($st, $body) = $anon->get('/services', false); // Accept: application/json
report('GET /services renders', $st === 200, "status=$st");
$json = json_decode($body, true);
$categories = $json['data']['categories'] ?? [];
report('Pillars exposed', count($categories) > 0, count($categories) . ' top-level categories');

$childSlugs = [];
foreach ($categories as $cat) {
    foreach (($cat['children'] ?? []) as $child) {
        $childSlugs[] = $child['slug'];
    }
}
$badCats = [];
foreach ($childSlugs as $slug) {
    list($s) = $anon->get('/services?category=' . urlencode($slug));
    if ($s !== 200) {
        $badCats[$slug] = $s;
    }
}
report('Every child category page returns 200', count($badCats) === 0,
    count($childSlugs) . ' checked' . (count($badCats) ? ' -> ' . json_encode($badCats) : ''));

$topSlugs = array_column($categories, 'slug');
$badTop = [];
foreach ($topSlugs as $slug) {
    list($s) = $anon->get('/services?category=' . urlencode($slug));
    if ($s !== 200) {
        $badTop[$slug] = $s;
    }
}
report('Every pillar page returns 200', count($badTop) === 0, count($topSlugs) . ' checked');

// Every active service detail page must render 200
$allSlugs = DB::table('services')->where('is_active', true)->pluck('slug');
$badSvc = [];
foreach ($allSlugs as $slug) {
    list($s) = $anon->get('/services/' . $slug);
    if ($s !== 200) {
        $badSvc[$slug] = $s;
    }
}
report('Every active service page returns 200', count($badSvc) === 0,
    count($allSlugs) . ' checked' . (count($badSvc) ? ' -> ' . json_encode($badSvc) : ''));

// Inactive service must 404
$inactive = DB::table('services')->where('is_active', false)->value('slug');
if ($inactive) {
    list($s) = $anon->get('/services/' . $inactive);
    report('Inactive service returns 404', $s === 404, "$inactive -> $s");
}

// ===============================================================
echo "\n--- 2. Customer submits an application (with file uploads) ---\n";

clearThrottles();

// Use a service that has required file fields to prove uploads work end to end.
$service = DB::table('services')->where('is_active', true)
    ->whereIn('slug', ['nida-registration-correction', 'tin-registration'])
    ->orderBy('id')->first();
$serviceId = $service->id;

list($st, $detailBody) = $anon->get('/services/' . $service->slug, false); // Accept: application/json
$detail = json_decode($detailBody, true);
$fields = $detail['data']['fields'] ?? [];
report('Service detail exposes dynamic fields', count($fields) > 0,
    count($fields) . " fields for {$service->slug}");

$textFields = [];
$fileFields = [];
foreach ($fields as $f) {
    if ($f['field_type'] === 'file') {
        $fileFields[$f['field_key']] = $f;
    } else {
        $textFields[$f['field_key']] = match ($f['field_type']) {
            'email' => 'e2e.audit@example.com',
            'tel' => '+255712345678',
            'date' => date('Y-m-d', strtotime('+7 days')),
            'number' => '12345',
            'select', 'radio' => $f['options'][0] ?? 'Option A',
            'textarea' => 'Automated connectivity audit entry.',
            default => 'E2E Test Value',
        };
    }
}
report('Text fields populated for payload', count($textFields) > 0, count($textFields) . ' text fields');
report('File fields will be uploaded', count($fileFields) > 0, count($fileFields) . ' file fields');

// Build a tiny real PDF so it passes mimes:pdf,jpg,png,doc,docx
$fakePdf = tempnam(sys_get_temp_dir(), 'e2e') . '.pdf';
file_put_contents($fakePdf, "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n"
    . "2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n"
    . "3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 200 200]>>endobj\n"
    . "trailer<</Root 1 0 R>>\n%%EOF\n");

$uploadPaths = [];
foreach ($fileFields as $key => $f) {
    $uploadPaths["fields[{$key}]"] = [$fakePdf, 'audit-test.pdf', 'application/pdf'];
}

// The last request was JSON, so refresh an HTML form page to harvest a plain token.
$anon->get('/contact');
$submitToken = $anon->csrf();
report('Fresh CSRF token for submission', $submitToken !== '', strlen($submitToken) . ' chars');

$payload = [
    '_token' => $submitToken,
    'service_id' => $serviceId,
    'customer_name' => 'E2E Audit Tester',
    'customer_phone' => '+255712345678',
    'customer_email' => 'e2e.audit@example.com',
    'customer_notes' => 'Automated connectivity audit.',
    'fields' => $textFields,
];

$before = (int) DB::table('submissions')->count();
list($st, $body) = $anon->postMultipart('/submit', $payload, $uploadPaths, true);
$json = json_decode($body, true);
$after = (int) DB::table('submissions')->count();

report('POST /submit accepted (201)', in_array($st, [200, 201], true),
    "status=$st " . substr($body, 0, 200));
report('Submission row persisted', $after === $before + 1, "count $before -> $after");

$ref = $json['data']['reference_number'] ?? null;
report('Response returns a reference number', ! empty($ref), (string) $ref);

$row = $ref ? DB::table('submissions')->where('reference_number', $ref)->first() : null;

// Only ever operate on a submission THIS run created — never touch pre-existing data.
$created = ($after === $before + 1) && $row;
$submissionId = $created ? $row->id : null;
$submissionRef = $created ? $row->reference_number : null;

if ($created) {
    report('Submission linked to correct service', (int) $row->service_id === (int) $serviceId, "service_id={$row->service_id}");
    report('Submission starts pending', $row->status === 'pending', $row->status);
    report('Reference format valid', (bool) preg_match('/^DSC-[0-9]{8}-[A-Z0-9]+$/', (string) $submissionRef), $submissionRef);

    $logRows = DB::table('activity_logs')->where('subject_id', $row->id)->count();
    report('Activity log written on create', $logRows > 0, "$logRows entries");

    $valueRows = DB::table('submission_field_values')->where('submission_id', $row->id)->count();
    report('Field values snapshotted', $valueRows > 0, "$valueRows rows");

    $fileRows = DB::table('submission_field_values')->where('submission_id', $row->id)
        ->whereNotNull('file_path')->count();
    report('Uploaded files stored to private disk', $fileRows === count($fileFields),
        "$fileRows of " . count($fileFields) . ' expected files');

    if ($fileRows > 0) {
        $samplePath = DB::table('submission_field_values')->where('submission_id', $row->id)
            ->whereNotNull('file_path')->value('file_path');
        $disk = storage_path('app/private/' . $samplePath);
        $alt = storage_path('app/' . $samplePath);
        report('File exists on disk', file_exists($disk) || file_exists($alt),
            (file_exists($disk) ? $disk : (file_exists($alt) ? $alt : "MISSING: $samplePath")));
    }

    $filled = DB::table('submission_field_values')->where('submission_id', $row->id)
        ->whereNotNull('value')->count();
    report('Text values actually stored', $filled > 0, "$filled of $valueRows have values");
}

// ---- validation guards ----
echo "\n--- 3. Validation guards ---\n";
clearThrottles();
$anon->get('/contact'); // refresh token
$t = $anon->csrf();

list($st) = $anon->post('/submit', ['customer_name' => 'x', '_token' => $t], true);
report('Missing service_id rejected', $st === 422, "status=$st");

list($st) = $anon->post('/submit', [
    'service_id' => 999999, 'customer_name' => 'x', 'customer_phone' => '1', '_token' => $t,
], true);
report('Nonexistent service rejected', in_array($st, [404, 422], true), "status=$st");

list($st) = $anon->post('/submit', [
    'service_id' => $serviceId, 'customer_name' => '', 'customer_phone' => '', '_token' => $t,
], true);
report('Missing name/phone rejected', $st === 422, "status=$st");

list($st) = $anon->post('/submit', [
    'service_id' => $serviceId, 'customer_name' => 'x', 'customer_phone' => '1', '_token' => 'bad-token',
], true);
report('Bad CSRF rejected', $st === 419, "status=$st");

// Required file field must be enforced (fresh token, no files attached)
$anon->get('/contact');
$t2 = $anon->csrf();
list($st, $body) = $anon->postMultipart('/submit', array_merge($payload, ['_token' => $t2]), [], true);
report('Required file field enforced (no file -> 422)', $st === 422, "status=$st");

// ===============================================================
echo "\n--- 4. Admin side ---\n";
clearThrottles();

$admin = new Browser('admin');
list($st, $body) = $admin->get('/admin/login');
report('Admin login page renders', $st === 200, "status=$st");
$adminToken = $admin->csrf();

list($st, $body) = $admin->post('/admin/login', [
    'email' => 'admin@digitalstar.local',
    'password' => 'Adm1n!Dsc2026#Xq9',
    '_token' => $adminToken,
]);
$onLogin = str_contains($body, 'admin-login-form');
report('Admin login succeeds', $st === 200 && ! $onLogin, "status=$st stillOnLogin=" . var_export($onLogin, true));
$adminToken = $admin->csrf();

if ($submissionId) {
    list($st, $body) = $admin->get('/admin/submissions/' . $submissionId);
    report('Admin detail shows new submission', $st === 200
        && (str_contains($body, 'E2E Audit Tester') || str_contains($body, (string) $submissionRef)),
        "status=$st");

    list($st, $body) = $admin->get('/admin/submissions?search=' . urlencode((string) $submissionRef));
    report('Admin search finds it by reference', $st === 200 && str_contains($body, (string) $submissionRef), "status=$st");

    // Admin can download the customer's uploaded file
    $fileValueId = DB::table('submission_field_values')->where('submission_id', $submissionId)
        ->whereNotNull('file_path')->value('id');
    if ($fileValueId) {
        list($s) = $admin->get("/admin/submissions/{$submissionId}/files/{$fileValueId}");
        report('Admin file download works', in_array($s, [200, 302], true), "status=$s");
    }
}

// ===============================================================
echo "\n--- 5. Status transitions propagate ---\n";

if ($submissionId) {
    $steps = [
        ['in-progress', 'in_progress'],
        ['awaiting-customer', 'awaiting_customer'],
        ['in-progress', 'in_progress'],
        ['complete', 'completed'],
    ];
    foreach ($steps as [$action, $expected]) {
        $data = ['_token' => $adminToken];
        if ($action === 'awaiting-customer') {
            $data['reason'] = 'Please upload the missing supporting document.';
        }
        list($s) = $admin->post("/admin/submissions/{$submissionId}/{$action}", $data);
        $db = DB::table('submissions')->where('id', $submissionId)->value('status');
        report("Transition -> $expected", $db === $expected, "http=$s db=$db");
    }

    $completedAt = DB::table('submissions')->where('id', $submissionId)->value('completed_at');
    report('completed_at stamped', ! empty($completedAt), (string) $completedAt);

    $logCount = DB::table('activity_logs')->where('subject_id', $submissionId)->count();
    report('Transitions logged to activity', $logCount >= 4, "$logCount activity entries");

    // ===============================================================
    echo "\n--- 6. Customer track page ---\n";

    list($s, $b) = $anon->get('/track/status/' . $submissionRef);
    report('Track page 200', $s === 200, "status=$s");
    report('Track shows reference', str_contains($b, (string) $submissionRef));
    report('Track reflects completed', stripos($b, 'completed') !== false);

    // Observe the redirect without following it
    $anon->follow = false;
    list($s) = $anon->get('/track?reference=' . urlencode((string) $submissionRef));
    report('Track form redirects to status page', $s === 302, "status=$s");
    $anon->follow = true;

    // Bogus reference: what does an HTML browser see?
    list($s, $b) = $anon->get('/track?reference=NOT-A-REAL-REF');
    report('Bogus reference handled', in_array($s, [200, 302, 422], true), "status=$s");
    if ($s === 422) {
        report('Bogus reference shows friendly page', false,
            'HTML browser gets raw 422 JSON instead of a friendly error');
    }

    // ===============================================================
    echo "\n--- 7. Rejection path ---\n";
    clearThrottles();

    list($s) = $admin->post("/admin/submissions/{$submissionId}/reject", [
        '_token' => $adminToken, 'reason' => 'E2E audit rejection test',
    ]);
    $db = DB::table('submissions')->where('id', $submissionId)->value('status');
    report('Reject works', $db === 'rejected', "http=$s db=$db");

    list($s, $b) = $anon->get('/track/status/' . $submissionRef);
    report('Track reflects rejection', stripos($b, 'not approved') !== false || stripos($b, 'rejected') !== false,
        "status=$s");
}

// ===============================================================
echo "\n--- 8. Admin page inventory ---\n";
clearThrottles();

$pages = [
    '/admin/dashboard' => 'Dashboard',
    '/admin/submissions' => 'Submissions',
    '/admin/services' => 'Services',
    '/admin/categories' => 'Categories',
    '/admin/users' => 'Users',
    '/admin/reports' => 'Reports',
    '/admin/reports/daily' => 'Daily',
    '/admin/reports/weekly' => 'Weekly',
    '/admin/reports/monthly' => 'Monthly',
    '/admin/reports/staff-performance' => 'Staff perf',
    '/admin/reports/service-usage' => 'Service usage',
    '/admin/finance' => 'Finance',
    '/admin/settings' => 'Settings',
    '/admin/contact-messages' => 'Contact inbox',
    '/admin/notifications' => 'Notifications',
];
$badPages = [];
foreach ($pages as $uri => $label) {
    list($s, $b) = $admin->get($uri);
    if ($s !== 200 || str_contains($b, 'admin-login-form')) {
        $badPages[$label] = $s;
    }
}
report('All admin pages load when authenticated', count($badPages) === 0,
    count($pages) . ' pages' . (count($badPages) ? ' -> ' . json_encode($badPages) : ' OK'));

$leak = [];
foreach (array_slice(array_keys($pages), 0, 6) as $uri) {
    list($s, $b) = $anon->get($uri);
    if (! str_contains($b, 'Sign in') && strlen($b) > 500) {
        $leak[$uri] = $s;
    }
}
report('Unauthenticated gets no admin content', count($leak) === 0,
    count($leak) === 0 ? 'all redirect to sign-in' : json_encode($leak));

// ===============================================================
echo "\n--- 9. Contact form pipeline ---\n";
clearThrottles();

$contact = new Browser('contact');
$contact->get('/contact');
$contactToken = $contact->csrf();

$before = (int) DB::table('contact_messages')->count();
list($s, $b) = $contact->post('/contact', [
    'name' => 'E2E Contact Tester',
    'email' => 'contact.e2e@example.com',
    'phone' => '+255700000000',
    'subject' => 'Connectivity audit',
    'message' => 'Automated contact form test.',
    '_token' => $contactToken,
]);
$after = (int) DB::table('contact_messages')->count();
report('POST /contact accepted', in_array($s, [200, 201], true), "status=$s");
report('Contact message persisted', $after === $before + 1, "count $before -> $after");

$contact->get('/contact');
$contact->follow = false; // observe the validation bounce instead of following it
list($s) = $contact->post('/contact', [
    'name' => '', 'email' => '', 'message' => '', '_token' => $contact->csrf(),
]);
report('Contact validation rejects empty', $s === 302, "status=$s (302 = bounced back with errors)");
$contact->follow = true;

list($s, $b) = $admin->get('/admin/contact-messages');
report('Admin contact inbox shows it', $s === 200 && str_contains($b, 'E2E Contact Tester'), "status=$s");

@unlink($fakePdf);

echo "\n============================================================\n";
printf(" RESULT: %d passed, %d failed\n", $pass, $fail);
echo "============================================================\n";
exit($fail > 0 ? 1 : 0);
