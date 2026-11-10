<?php
/** One-off: identify empty categories, missing descriptions, and dead links. */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;

echo "=== ACTIVE CATEGORIES WITH NO ACTIVE SERVICES ===\n";
$withServices = Service::where('is_active', true)->pluck('service_category_id')->filter()->unique();
$empty = ServiceCategory::where('is_active', true)->whereNotIn('id', $withServices)->get();
foreach ($empty as $c) {
    $total = Service::where('service_category_id', $c->id)->count();
    printf("  #%d %-28s slug=%-24s parent=%s (total services incl. inactive: %d)\n",
        $c->id, $c->name, $c->slug, $c->parent_id ?? 'ROOT', $total);
}

echo "\n=== SERVICES MISSING DESCRIPTION ===\n";
$missing = Service::where('is_active', true)
    ->where(fn ($q) => $q->whereNull('description')->orWhere('description', ''))
    ->with('category')->get();
echo "  {$missing->count()} of " . Service::where('is_active', true)->count() . " active services\n";
$byCat = $missing->groupBy(fn ($s) => $s->category?->name ?? '(no category)');
foreach ($byCat as $cat => $svcs) {
    printf("  %-30s %d  (%s)\n", $cat, $svcs->count(), $svcs->pluck('slug')->take(4)->implode(', '));
}

echo "\n=== INACTIVE SERVICE LINKED ANYWHERE? ===\n";
$inactive = Service::where('is_active', false)->get();
foreach ($inactive as $s) {
    printf("  %s (slug=%s, category=%s)\n", $s->name, $s->slug, $s->category?->name);
}
// Is its slug referenced in any view?
$views = resource_path('views');
$hits = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($views));
foreach ($it as $file) {
    if (! $file->isFile() || ! preg_match('/\.(blade\.php|php)$/', $file->getFilename())) {
        continue;
    }
    $c = @file_get_contents($file->getPathname());
    if ($c && str_contains($c, 'visa-application')) {
        $hits[] = str_replace($views, 'views', $file->getPathname());
    }
}
echo '  referenced in views: ' . ($hits ? implode(', ', $hits) : 'none') . "\n";

echo "\n=== SUBMISSIONS FOR INACTIVE SERVICE ===\n";
foreach ($inactive as $s) {
    $n = DB::table('submissions')->where('service_id', $s->id)->count();
    echo "  {$s->name}: {$n} submission(s)\n";
}

echo "\n=== PRICE / DURATION COVERAGE ===\n";
echo '  services with price set: ' . Service::where('is_active', true)->whereNotNull('price')->where('price', '>', 0)->count() . "\n";
echo '  services with duration set: ' . Service::where('is_active', true)->whereNotNull('duration')->where('duration', '!=', '')->count() . "\n";

echo "\n=== NOTIFICATIONS: READ/UNREAD ===\n";
try {
    $n = DB::table('notifications');
    echo '  total: ' . $n->count() . ', unread: ' . $n->whereNull('read_at')->count() . "\n";
} catch (Throwable $e) {
    echo '  missing table' . "\n";
}

echo "\n=== QUEUE: pending jobs ===\n";
try {
    echo '  jobs pending: ' . DB::table('jobs')->count() . "\n";
} catch (Throwable $e) {
    echo '  no jobs table' . "\n";
}
