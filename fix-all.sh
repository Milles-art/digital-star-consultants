#!/bin/bash
# Fix: Missing RegisterController + Seed all Tanzania government services
set -e

echo "🔧 Fixing missing files and seeding all services..."

# Fix 1: Recreate RegisterController
cat > app/Http/Controllers/Auth/RegisterController.php << 'CONTROLLER'
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm(): JsonResponse
    {
        return response()->json([
            'message' => 'Registration endpoint. Send POST request with name, email, phone.',
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
        ]);

        $tempPassword = Str::random(12);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($tempPassword),
            'role' => User::ROLE_STAFF,
            'is_active' => true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Account created successfully.',
            'data' => [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 201);
    }
}
CONTROLLER
echo "✅ Recreated RegisterController.php"

# Fix 2: Seed ALL Tanzania government services
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
echo "🎉 All fixes applied!"
echo "============================================================"
echo ""
echo "Now run:"
echo "  php artisan test --compact"
echo "  git add -A && git commit -m 'fix: recreate RegisterController, seed all govt services' && git push"
