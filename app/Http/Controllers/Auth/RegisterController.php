<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendWelcomeEmailJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (! request()->expectsJson()) {
            return view('auth.register');
        }

        return response()->json([
            'message' => 'Registration endpoint. Send POST request with user details.',
        ]);
    }

    public function register(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        // Generate random password (user will reset later)
        $tempPassword = Str::random(12);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($tempPassword),
            'role' => User::ROLE_STAFF,
            'is_active' => true,
        ]);

        SendWelcomeEmailJob::dispatch($user, $tempPassword);

        if (! $request->expectsJson()) {
            return redirect()->route('login')->with('success', 'Your account was created. Check your welcome email for access details.');
        }

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
            ],
        ], 201);
    }
}
