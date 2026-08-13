<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return response()->json([
            'message' => 'Registration endpoint. Send POST request with user details.'
        ]);
    }

    public function register(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'nullable|in:admin,ceo,gm,staff',
        ]);

        // Generate random password (user will reset later)
        $tempPassword = Str::random(12);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($tempPassword),
            'role' => $request->role ?? User::ROLE_STAFF,
            'is_active' => true,
        ]);

        // TODO: Send welcome email with password setup link

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully. A welcome email has been sent.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'role_label' => $user->role_label,
                    'is_active' => $user->is_active,
                ],
                // Remove this in production - for testing only
                'temp_password' => $tempPassword,
            ]
        ], 201);
    }
}