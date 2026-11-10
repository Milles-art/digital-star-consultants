<?php
/** One-off: print the full category tree with service counts. */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Service;
use App\Models\ServiceCategory;

$tops = ServiceCategory::whereNull('parent_id')->orderBy('sort_order')->get();

foreach ($tops as $top) {
    $direct = Service::where('service_category_id', $top->id)->where('is_active', true)->count();
    $kids = $top->children()->orderBy('sort_order')->get();
    $kidTotal = Service::whereIn('service_category_id', $kids->pluck('id'))->where('is_active', true)->count();

    printf("PILLAR  #%-2d %-34s slug=%-30s direct=%d children=%d childServices=%d %s\n",
        $top->id, $top->name, $top->slug, $direct, $kids->count(), $kidTotal,
        ($direct === 0 && $kidTotal === 0) ? '  <<< EMPTY' : '');

    foreach ($kids as $k) {
        $n = Service::where('service_category_id', $k->id)->where('is_active', true)->count();
        printf("   CHILD #%-2d %-32s slug=%-28s services=%d %s\n",
            $k->id, $k->name, $k->slug, $n, $n === 0 ? '  <<< EMPTY' : '');
    }
}

echo "\n=== services table columns ===\n";
echo implode(', ', Schema::getColumnListing('services')) . "\n";

echo "\n=== do any services have price/duration? ===\n";
echo '  price col present: ' . var_export(in_array('price', Schema::getColumnListing('services'), true), true) . "\n";
echo '  duration col present: ' . var_export(in_array('duration', Schema::getColumnListing('services'), true), true) . "\n";
