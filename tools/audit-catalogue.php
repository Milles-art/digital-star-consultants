<?php
/**
 * One-off: report catalogue + pipeline integrity for the functional audit.
 * Usage: php tools/audit-catalogue.php
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceField;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function line(string $k, $v): void
{
    printf("%-52s %s\n", $k, $v);
}

echo "=== CATALOGUE ===\n";
line('Categories total / active', ServiceCategory::count() . ' / ' . ServiceCategory::where('is_active', true)->count());
line('  top-level / children', ServiceCategory::whereNull('parent_id')->count() . ' / ' . ServiceCategory::whereNotNull('parent_id')->count());
line('Services total / active', Service::count() . ' / ' . Service::where('is_active', true)->count());
line('Fields total / active', ServiceField::count() . ' / ' . ServiceField::where('is_active', true)->count());

/**
 * Run one audit check, never letting a query error abort the whole report.
 */
function check(string $label, callable $fn): void
{
    try {
        line($label, $fn());
    } catch (Throwable $e) {
        line($label, 'ERROR: ' . $e->getMessage());
    }
}

echo "\n=== INTEGRITY ===\n";
check('Active services with NO active fields', function () {
    $withFields = ServiceField::where('is_active', true)->distinct()->pluck('service_id');
    return Service::where('is_active', true)->whereNotIn('id', $withFields)->count();
});
check('Active services with NO category', fn () => Service::where('is_active', true)->whereNull('service_category_id')->count());
check('Active services in an INACTIVE category', function () {
    $inactiveCats = ServiceCategory::where('is_active', false)->pluck('id');
    return Service::where('is_active', true)->whereIn('service_category_id', $inactiveCats)->count();
});
check('Active categories with NO services', function () {
    $withServices = Service::where('is_active', true)->pluck('service_category_id')->filter()->unique();
    return ServiceCategory::where('is_active', true)->whereNotIn('id', $withServices)->count();
});
check('Duplicate service slugs', fn () => Service::select('slug')->groupBy('slug')->havingRaw('COUNT(*)>1')->count());
check('Duplicate category slugs', fn () => ServiceCategory::select('slug')->groupBy('slug')->havingRaw('COUNT(*)>1')->count());
check('Services missing description', fn () => Service::whereNull('description')->orWhere('description', '')->count());

$inactiveSvcs = Service::where('is_active', false)->pluck('name');
line('Inactive services', $inactiveSvcs->count() ? $inactiveSvcs->implode(' | ') : '(none)');
$inactiveCats = ServiceCategory::where('is_active', false)->pluck('name');
line('Inactive categories', $inactiveCats->count() ? $inactiveCats->implode(' | ') : '(none)');

echo "\n=== FIELD ORDER / TYPES ===\n";
line('Field types in use', ServiceField::select('field_type', DB::raw('COUNT(*) c'))
    ->groupBy('field_type')->orderByDesc('c')->pluck('c', 'field_type')->map(fn ($c, $t) => "$t=$c")->join(', '));
line('Fields with no options but select/radio type', ServiceField::whereIn('field_type', ['select', 'radio'])
    ->where(fn ($q) => $q->whereNull('options')->orWhere('options', '')->orWhere('options', '[]'))->count());
line('Required fields', ServiceField::where('is_required', true)->count());

echo "\n=== SUBMISSIONS ===\n";
line('Total submissions', Submission::count());
line('By status', Submission::select('status', DB::raw('COUNT(*) c'))->groupBy('status')
    ->pluck('c', 'status')->map(fn ($c, $s) => "$s=$c")->join(', ') ?: '(none)');
check('Orphan (service_id missing/deleted)', fn () => Submission::whereDoesntHave('service')->count());
check('Orphan (service inactive)', function () {
    $inactive = Service::where('is_active', false)->pluck('id');
    return Submission::whereIn('service_id', $inactive)->count();
});
check('Assigned but staff user missing', function () {
    $ids = User::pluck('id');
    return Submission::whereNotNull('assigned_to')->whereNotIn('assigned_to', $ids)->count();
});
check('Processed-by user missing', function () {
    $ids = User::pluck('id');
    return Submission::whereNotNull('processed_by')->whereNotIn('processed_by', $ids)->count();
});
check('Duplicate reference numbers', function () {
    $dups = Submission::select('reference_number')->groupBy('reference_number')->havingRaw('COUNT(*)>1')->pluck('reference_number');
    return $dups->count() ? $dups->implode(', ') : 0;
});

echo "\n=== USERS ===\n";
line('Total / active', User::count() . ' / ' . User::where('is_active', true)->count());
line('By role', User::select('role', DB::raw('COUNT(*) c'))->groupBy('role')
    ->pluck('c', 'role')->map(fn ($c, $r) => "$r=$c")->join(', '));
line('Management users (admin/ceo/gm)', User::whereIn('role', ['admin', 'ceo', 'gm'])->count());
line('Staff users', User::where('role', 'staff')->count());
line('Users with NULL password', User::whereNull('password')->count());

echo "\n=== QUEUE / JOBS ===\n";
try {
    line('jobs table rows', DB::table('jobs')->count());
} catch (Throwable $e) {
    line('jobs table', 'missing: ' . $e->getMessage());
}
try {
    line('failed_jobs rows', DB::table('failed_jobs')->count());
} catch (Throwable $e) {
    line('failed_jobs', 'missing');
}

echo "\n=== ACTIVITY LOG / NOTIFICATIONS ===\n";
try {
    line('activity_logs rows', DB::table('activity_logs')->count());
} catch (Throwable $e) {
    line('activity_logs', 'missing');
}
try {
    line('notifications rows', DB::table('notifications')->count());
} catch (Throwable $e) {
    line('notifications', 'missing');
}
try {
    line('contact_messages rows', DB::table('contact_messages')->count());
} catch (Throwable $e) {
    line('contact_messages', 'missing');
}

echo "\nDONE\n";
