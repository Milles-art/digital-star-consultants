#!/bin/bash
# Fix: Controller not loading service fields
set -e

echo "🔧 Fixing controller to load service fields..."

php << 'PHPEOF'
<?php
$content = file_get_contents("app/Http/Controllers/Public/ServiceController.php");

// Fix show() method to eager-load fields and category
$old = <<<'OLD'
    public function show($slug)
    {
        $service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('services.show', compact('service'));
    }
OLD;

$new = <<<'NEW'
    public function show($slug)
    {
        $service = Service::with(['fields', 'category'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('services.show', compact('service'));
    }
NEW;

if (strpos($content, $old) !== false) {
    $content = str_replace($old, $new, $content);
    file_put_contents("app/Http/Controllers/Public/ServiceController.php", $content);
    echo "✅ Fixed Public/ServiceController.php\n";
} else {
    echo "ℹ️  Controller already fixed or different structure\n";
}
PHPEOF

echo ""
echo "============================================================"
echo "🎉 Controller fixed!"
echo "============================================================"
echo ""
echo "The controller now loads service fields. Run:"
echo "  php artisan test --compact"
echo "  git add -A && git commit -m 'fix: eager-load service fields in controller' && git push"
