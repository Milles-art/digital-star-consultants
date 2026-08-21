#!/bin/bash
# Fix: Controller must eager-load fields relationship
set -e

echo "🔧 Fixing controller to load service fields..."

# Show current controller content
echo ""
echo "📄 Current show() method:"
grep -A 10 "public function show" app/Http/Controllers/Public/ServiceController.php || echo "  (not found)"

# Fix it using sed - replace the show method
php << 'PHPEOF'
<?php
$file = 'app/Http/Controllers/Public/ServiceController.php';
$content = file_get_contents($file);

// Replace the show method to eager-load fields
$old = <<<'OLD'
    public function show($slug)
    {
        $service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
OLD;

$new = <<<'NEW'
    public function show($slug)
    {
        $service = Service::with(['category', 'fields'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
NEW;

if (strpos($content, $old) !== false) {
    $content = str_replace($old, $new, $content);
    file_put_contents($file, $content);
    echo "✅ Fixed controller: added ->with(['category', 'fields'])\n";
} else {
    // Check if already fixed
    if (strpos($content, "with(['category', 'fields'])") !== false) {
        echo "ℹ️  Controller already has fields eager-load\n";
    } else {
        echo "⚠️  Could not find expected code. Manual check needed.\n";
        echo "Current content around show():\n";
        // Show context
        $lines = explode("\n", $content);
        foreach ($lines as $i => $line) {
            if (strpos($line, 'function show') !== false) {
                for ($j = $i; $j < min($i + 15, count($lines)); $j++) {
                    echo "  " . ($j+1) . ": " . $lines[$j] . "\n";
                }
                break;
            }
        }
    }
}
PHPEOF

# Clear all caches
echo ""
echo "🧹 Clearing caches..."
php artisan view:clear
php artisan cache:clear
php artisan config:clear

echo ""
echo "============================================================"
echo "🎉 Fix applied!"
echo "============================================================"
echo ""
echo "Now refresh your browser (Ctrl+Shift+R) and visit:"
echo "  http://localhost:8000/services/tin-application"
echo ""
echo "You should now see the NIDA Number, TIN Number, and other fields!"
