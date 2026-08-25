<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showForgotForm(): JsonResponse
    {
        return response()->json([
            'message' => 'Forgot password endpoint. Send POST request with email.',
        ]);
    }

    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'If an account exists for that email, a password reset link has been sent.',
        ]);
    }

    public function showResetForm(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $reset || ! Hash::check($request->token, $reset->token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired token',
            ], 400);
        }

        if (now()->diffInMinutes($reset->created_at) > 60) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reset token has expired. Please request a new one.',
            ], 400);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successfully',
        ]);
    }
}
