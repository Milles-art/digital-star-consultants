<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        if (! request()->expectsJson()) {
            return view('auth.forgot-password');
        }

        return response()->json([
            'message' => 'Forgot password endpoint. Send POST request with email.',
        ]);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        Password::sendResetLink($request->only('email'));

        return $this->resetLinkResponse($request);
    }

    public function showResetForm($token)
    {
        if (! request()->expectsJson()) {
            return view('auth.reset-password', compact('token'));
        }

        return response()->json([
            'message' => 'Reset password endpoint. Send POST request with token, email, and password.',
            'token' => $token,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            },
        );

        if ($status !== Password::PasswordReset) {
            if (! $request->expectsJson()) {
                return back()->withErrors(['token' => 'This password reset link is invalid or expired.']);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired token',
            ], 400);
        }

        if (! $request->expectsJson()) {
            return redirect()->route('login')->with('success', 'Your password has been reset.');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successfully',
        ]);
    }

    private function resetLinkResponse(Request $request)
    {
        if (! $request->expectsJson()) {
            return redirect()->route('password.request')->with('success', 'If an account exists for that email, a password reset link has been sent.');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'If an account exists for that email, a password reset link has been sent.',
        ]);
    }
}
