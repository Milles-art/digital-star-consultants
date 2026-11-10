<?php
/**
 * One-off: test the staff workflow end-to-end against the LIVE server —
 * admin assigns, staff logs in, staff sees ONLY their assignment and can act.
 */
const BASE = 'http://127.0.0.1:8000';

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$pass = 0;
$fail = 0;

function report(string $label, bool $ok, string $detail = ''): void
{
    global $pass, $fail;
    $ok ? $pass++ : $fail++;
    printf("[%s] %-52s %s\n", $ok ? 'PASS' : 'FAIL', $label, $detail);
}

class Browser
{
    public $ch;
    public string $jar;
    public ?string $lastBody = null;
    public bool $follow = true;

    public function __construct(string $name)
    {
        $this->jar = sys_get_temp_dir() . "/dsc-staff-{$name}-" . getmypid() . '.cookies';
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

    public function get(string $path, bool $html = true): array
    {
        curl_reset($this->ch);
        curl_setopt_array($this->ch, $this->base());
        curl_setopt($this->ch, CURLOPT_URL, BASE . $path);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
            'Accept: ' . ($html ? 'text/html,application/xhtml+xml,*/*;q=0.8' : 'application/json'),
        ]);
        $out = curl_exec($this->ch);
        $this->lastBody = is_string($out) ? $out : '';

        return [(int) curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE), $this->lastBody];
    }

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

    /** PUT form data (for routes declared as PUT, e.g. staff notes). */
    public function put(string $path, array $data, bool $expectJson = false): array
    {
        curl_reset($this->ch);
        curl_setopt_array($this->ch, $this->base());
        curl_setopt($this->ch, CURLOPT_URL, BASE . $path);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: ' . ($expectJson ? 'application/json' : 'text/html,application/xhtml+xml,*/*;q=0.8'),
        ]);
        $out = curl_exec($this->ch);
        $this->lastBody = is_string($out) ? $out : '';

        return [(int) curl_getinfo($this->ch, CURLINFO_RESPONSE_CODE), $this->lastBody];
    }

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

    public function __destruct()
    {
        @unlink($this->jar);
    }
}

try {
    DB::table('cache')->delete();
} catch (Throwable $e) {
}

echo "============================================================\n";
echo " STAFF WORKFLOW AUDIT\n";
echo "============================================================\n\n";

// Pick two submissions so we can test assignment visibility (assigned vs not)
$subA = DB::table('submissions')->where('status', 'pending')->orderBy('id')->first();
$subB = DB::table('submissions')->where('status', 'pending')->where('id', '!=', $subA->id)->orderBy('id')->first();
$staff = DB::table('users')->where('role', 'staff')->where('is_active', true)->orderBy('id')->first();

echo "--- Setup ---\n";
report('Found a pending submission', (bool) $subA, $subA ? "#{$subA->id} {$subA->reference_number}" : 'none');
report('Found a second pending submission', (bool) $subB, $subB ? "#{$subB->id} {$subB->reference_number}" : 'none');
report('Found an active staff user', (bool) $staff, $staff ? "{$staff->email}" : 'none');

// Reset assignment so the test starts from a known state
$originalProcessedBy = $subA->processed_by ?? null;
DB::table('submissions')->where('id', $subA->id)->update(['processed_by' => null]);

// ===============================================================
echo "\n--- 1. Admin assigns submission to staff ---\n";

$admin = new Browser('admin');
$admin->get('/admin/login');
$adminToken = $admin->csrf();
$admin->post('/admin/login', [
    'email' => 'admin@digitalstar.local',
    'password' => 'Adm1n!Dsc2026#Xq9',
    '_token' => $adminToken,
]);
$adminToken = $admin->csrf();
$onLogin = str_contains($admin->lastBody ?? '', 'admin-login-form');
report('Admin logged in', ! $onLogin);

$admin->follow = false;
list($st) = $admin->post("/admin/submissions/{$subA->id}/assign", [
    '_token' => $adminToken, 'staff_id' => $staff->id,
]);
$admin->follow = true;
$assignedTo = DB::table('submissions')->where('id', $subA->id)->value('processed_by');
report('Assignment persisted to DB', (int) $assignedTo === (int) $staff->id,
    "http=$st processed_by=" . var_export($assignedTo, true));

$activity = DB::table('activity_logs')->where('subject_id', $subA->id)
    ->where('event', 'like', '%assign%')->count();
report('Assignment logged to activity', $activity > 0, "$activity assign log(s)");

// ===============================================================
echo "\n--- 2. Staff logs in via /login ---\n";

$staffBrowser = new Browser('staff');
$staffBrowser->get('/login');
$staffToken = $staffBrowser->csrf();
report('Staff login page renders', $staffToken !== '');

list($st, $body) = $staffBrowser->post('/login', [
    'email' => $staff->email,
    'password' => 'Stf1!Dsc2026#Wn6',
    '_token' => $staffToken,
]);
$stillLogin = str_contains($body, 'name="password"');
report('Staff login succeeds', ! $stillLogin && $st === 200, "status=$st stillOnLogin=" . var_export($stillLogin, true));

// ===============================================================
echo "\n--- 3. Staff sees only their assigned work ---\n";

list($st, $body) = $staffBrowser->get('/staff/submissions');
report('Staff submissions page loads', $st === 200, "status=$st");
report('Staff sees the ASSIGNED submission', str_contains($body, $subA->reference_number),
    $subA->reference_number);
report('Staff does NOT see the UNASSIGNED submission', ! str_contains($body, $subB->reference_number),
    $subB->reference_number . ' hidden');

// ===============================================================
echo "\n--- 4. Staff authorization boundaries ---\n";

// Staff must NOT be able to view an unassigned submission directly
$staffBrowser->follow = false;
list($st) = $staffBrowser->get('/staff/submissions/' . $subB->id);
report('Staff blocked from unassigned submission (403)', in_array($st, [403, 404], true), "status=$st");

list($st) = $staffBrowser->get('/staff/submissions/' . $subA->id);
report('Staff allowed into assigned submission (200)', $st === 200, "status=$st");

// Staff must NOT reach admin routes
list($st) = $staffBrowser->get('/admin/submissions');
report('Staff blocked from /admin (403)', in_array($st, [403], true), "status=$st");

list($st) = $staffBrowser->get('/admin/users');
report('Staff blocked from /admin/users (403)', in_array($st, [403], true), "status=$st");

list($st) = $staffBrowser->get('/admin/settings');
report('Staff blocked from /admin/settings (403)', in_array($st, [403], true), "status=$st");

$staffBrowser->follow = true;

// ===============================================================
echo "\n--- 5. Staff can process their own submission ---\n";

$staffBrowser->get('/staff/submissions/' . $subA->id);
$staffToken = $staffBrowser->csrf();

$steps = [
    ['in-progress', 'in_progress'],
    ['complete', 'completed'],
];
foreach ($steps as [$action, $expected]) {
    $staffBrowser->follow = false;
    list($st) = $staffBrowser->post("/staff/submissions/{$subA->id}/{$action}", ['_token' => $staffToken]);
    $staffBrowser->follow = true;
    $db = DB::table('submissions')->where('id', $subA->id)->value('status');
    report("Staff transition -> $expected", $db === $expected, "http=$st db=$db");
    $staffToken = $staffBrowser->csrf();
}

$completedAt = DB::table('submissions')->where('id', $subA->id)->value('completed_at');
report('Staff completion stamps completed_at', ! empty($completedAt), (string) $completedAt);

// ===============================================================
echo "\n--- 6. Staff notes update ---\n";

$staffBrowser->get('/staff/submissions/' . $subA->id);
$staffToken = $staffBrowser->csrf();
$staffBrowser->follow = false;
list($st) = $staffBrowser->put("/staff/submissions/{$subA->id}/notes", [
    '_token' => $staffToken, 'staff_notes' => 'Audit test: staff notes update.',
], true);
$staffBrowser->follow = true;
$notes = DB::table('submissions')->where('id', $subA->id)->value('staff_notes');
report('Staff notes persisted', str_contains((string) $notes, 'Audit test'), "http=$st");

// ===============================================================
echo "\n--- 7. Staff blocked from REJECT (management-only per policy) ---\n";

$staffBrowser->get('/staff/submissions/' . $subA->id);
$staffToken = $staffBrowser->csrf();
$staffBrowser->follow = false;
list($st) = $staffBrowser->post("/staff/submissions/{$subA->id}/reject", [
    '_token' => $staffToken, 'reason' => 'should be blocked',
]);
$staffBrowser->follow = true;
$db = DB::table('submissions')->where('id', $subA->id)->value('status');
report('Staff cannot reject (policy)', in_array($st, [403], true) && $db === 'completed',
    "http=$st db=$db");

// ===============================================================
echo "\n--- 8. Restore submission to its original state ---\n";

DB::table('submissions')->where('id', $subA->id)->update([
    'status' => 'pending',
    'processed_by' => $originalProcessedBy,
    'staff_notes' => null,
    'completed_at' => null,
]);
$restored = DB::table('submissions')->where('id', $subA->id)->first();
report('Submission restored', $restored->status === 'pending',
    "status={$restored->status} processed_by=" . var_export($restored->processed_by, true));

try {
    DB::table('cache')->delete();
} catch (Throwable $e) {
}

echo "\n============================================================\n";
printf(" RESULT: %d passed, %d failed\n", $pass, $fail);
echo "============================================================\n";
exit($fail > 0 ? 1 : 0);
