<?php
/** One-off: remove the audit test's own artifacts (submission #9, contact probes). */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// 1. Remove the submission this audit created (id 9, DSC-20261026-*).
$created = DB::table('submissions')
    ->where('reference_number', 'like', 'DSC-20261026-%')
    ->get();
foreach ($created as $s) {
    DB::table('submission_field_values')->where('submission_id', $s->id)->delete();
    DB::table('activity_logs')->where('subject_type', 'App\Models\Submission')
        ->where('subject_id', $s->id)->delete();
    DB::table('submissions')->where('id', $s->id)->delete();
    echo "removed submission #{$s->id} ({$s->reference_number})\n";
}

// 2. Remove uploaded test files on disk.
$dir = storage_path('app/private/submissions/9');
if (is_dir($dir)) {
    array_map('unlink', glob($dir . '/*') ?: []);
    rmdir($dir);
    echo "removed uploaded test files\n";
}

// 3. Remove contact messages created by the audit.
$deleted = DB::table('contact_messages')
    ->whereIn('email', ['contact.e2e@example.com', 'probe@example.com', 'bodyprobe@example.com',
        'fresh@example.com', 'fresh2@example.com', 'control@example.com'])
    ->orWhere('name', 'like', '%Probe%')
    ->orWhere('name', 'like', '%E2E%')
    ->delete();
echo "removed {$deleted} audit contact messages\n";

// 4. Reset the rate-limit cache so normal use isn't throttled.
try {
    DB::table('cache')->delete();
    echo "cleared rate-limit cache\n";
} catch (Throwable $e) {
    // no cache table
}

echo "\nCurrent submissions:\n";
foreach (DB::table('submissions')->orderBy('id')->get() as $s) {
    printf("  #%-3d %-22s %-18s svc=%-3s %s\n", $s->id, $s->reference_number, $s->status, $s->service_id, $s->customer_name);
}
echo "\nContact messages: " . DB::table('contact_messages')->count() . "\n";
