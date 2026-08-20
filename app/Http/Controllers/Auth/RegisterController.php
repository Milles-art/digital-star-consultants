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
        // NOTE: intentionally no "role" field is accepted here. This endpoint
        // is public and unauthenticated — allowing role selection let anyone
        // create an admin/ceo/gm account. Management accounts must only be
        // created via the authenticated Admin\UserController::store flow.
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        // Generate random password (user will reset later)
        $tempPassword = Str::random(12);

        // Create user — role is always forced to staff on this public endpoint
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($tempPassword),
            'role' => User::ROLE_STAFF,
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
                // temp_password intentionally NOT returned here — it is only
                // delivered via the welcome email once that job is wired up.
            ]
        ], 201);
    }
}
