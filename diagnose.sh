#!/bin/bash
# Diagnose and fix missing files + empty database
set -e

echo "🔍 Diagnosing issues..."

# Check database
echo ""
echo "📊 Database counts:"
php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo 'Services: ' . \App\Models\Service::count() . PHP_EOL; echo 'ServiceFields: ' . \App\Models\ServiceField::count() . PHP_EOL; echo 'ServiceCategories: ' . \App\Models\ServiceCategory::count() . PHP_EOL;"

# Check missing controllers
echo ""
echo "📁 Checking controllers:"
for file in "app/Http/Controllers/Auth/RegisterController.php" "app/Http/Controllers/Auth/LoginController.php" "app/Http/Controllers/Auth/PasswordResetController.php"; do
    if [ -f "$file" ]; then
        echo "  ✅ $file"
    else
        echo "  ❌ MISSING: $file"
    fi
done

# Check views
echo ""
echo "📁 Checking views:"
ls -la resources/views/ 2>/dev/null | head -20

echo ""
echo "============================================================"
echo "🔧 Run the appropriate fix below:"
echo "============================================================"
echo ""
