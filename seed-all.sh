#!/bin/bash
# Create ALL required categories with exact slugs, then seed everything
set -e

echo "🔧 Creating all required categories..."

php << 'PHPEOF'
<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ServiceCategory;

// These are the EXACT slugs each seeder looks for
$required = [
    ['slug' => 'serikali-identification', 'name' => 'Serikali & Identification', 'description' => 'Government ID and citizen services', 'icon' => '🏛️', 'sort_order' => 1],
    ['slug' => 'tax-tra', 'name' => 'Tax & TRA', 'description' => 'Tanzania Revenue Authority services', 'icon' => '💰', 'sort_order' => 2],
    ['slug' => 'travel-tourism', 'name' => 'Travel & Tourism', 'description' => 'Travel and tourism services', 'icon' => '✈️', 'sort_order' => 3],
    ['slug' => 'business-brela', 'name' => 'Business & BRELA', 'description' => 'Business registration services', 'icon' => '🏢', 'sort_order' => 4],
];

foreach ($required as $cat) {
    $existing = ServiceCategory::where('slug', $cat['slug'])->first();
    if ($existing) {
        echo "  ℹ️  Category exists: {$cat['slug']}\n";
    } else {
        ServiceCategory::create($cat + ['is_active' => true]);
        echo "  ✅ Created: {$cat['slug']}\n";
    }
}

echo "\n📊 Current state:\n";
echo "  Categories: " . ServiceCategory::count() . "\n";
echo "  Services: " . \App\Models\Service::count() . "\n";
echo "  Fields: " . \App\Models\ServiceField::count() . "\n";
PHPEOF

echo ""
echo "🌱 Seeding all Tanzania government services..."

# Seed one by one with error handling
for seeder in SerikaliIdentificationServiceSeeder TraServiceSeeder TravelServiceSeeder BrelaBusinessServiceSeeder; do
    echo ""
    echo "→ Seeding $seeder..."
    php artisan db:seed --class=$seeder || echo "  ⚠️  $seeder failed (may already be seeded)"
done

echo ""
echo "📊 Final database state:"
php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo '  Categories: ' . \App\Models\ServiceCategory::count() . PHP_EOL; echo '  Services: ' . \App\Models\Service::count() . PHP_EOL; echo '  Fields: ' . \App\Models\ServiceField::count() . PHP_EOL;"

echo ""
echo "============================================================"
echo "🎉 Done!"
echo "============================================================"
