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

        $user = User::create($request->only(['name', 'email', 'phone']));
        $user->password = Hash::make($tempPassword);
        $user->role = User::ROLE_STAFF;
        $user->is_active = true;
        $user->save();

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
