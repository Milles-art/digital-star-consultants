#!/bin/bash
# Fix: Create required categories, then seed all services
set -e

echo "🔧 Creating categories and seeding all services..."

php << 'PHPEOF'
<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ServiceCategory;

$categories = [
    ['name' => 'Serikali & Identification', 'slug' => 'serikali-identification', 'description' => 'Government identification and citizen services', 'icon' => '🏛️', 'sort_order' => 1],
    ['name' => 'Tax & TRA', 'slug' => 'tax-tra', 'description' => 'Tanzania Revenue Authority tax services', 'icon' => '💰', 'sort_order' => 2],
    ['name' => 'Travel & Tourism', 'slug' => 'travel-tourism', 'description' => 'Travel booking and tourism services', 'icon' => '✈️', 'sort_order' => 3],
    ['name' => 'Business & BRELA', 'slug' => 'business-brela', 'description' => 'Business registration and licensing', 'icon' => '🏢', 'sort_order' => 4],
];

foreach ($categories as $cat) {
    ServiceCategory::updateOrCreate(['slug' => $cat['slug']], $cat + ['is_active' => true]);
}

echo "✅ Created " . count($categories) . " categories\n";
PHPEOF

# Now seed all services
echo ""
echo "🌱 Seeding all Tanzania government services..."
php artisan db:seed --class=SerikaliIdentificationServiceSeeder
php artisan db:seed --class=TraServiceSeeder
php artisan db:seed --class=TravelServiceSeeder
php artisan db:seed --class=BrelaBusinessServiceSeeder

echo ""
echo "📊 Verifying database..."
php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo 'Services: ' . \App\Models\Service::count() . PHP_EOL; echo 'ServiceFields: ' . \App\Models\ServiceField::count() . PHP_EOL; echo 'ServiceCategories: ' . \App\Models\ServiceCategory::count() . PHP_EOL;"

echo ""
echo "============================================================"
echo "🎉 All services seeded!"
echo "============================================================"
echo ""
echo "Now run:"
echo "  php artisan test --compact"
echo "  git add -A && git commit -m 'fix: add categories, seed all govt services, recreate RegisterController' && git push"
