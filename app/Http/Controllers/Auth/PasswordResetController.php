<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = strtolower(trim($request->string('email')->toString()));
        $user = User::where('email', $email)->first();

        // Always return the same public response, whether or not the account
        // exists, so this endpoint cannot be used for user enumeration.
        if ($user && $user->is_active) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            $user->notify(new PasswordResetNotification($token));
        }

        $message = 'If an account exists for that email, a password reset link has been sent.';

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function showResetForm(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string', 'size:64'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        $email = strtolower(trim($request->string('email')->toString()));
        $reset = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (! $reset || ! Hash::check($request->token, $reset->token) || now()->diffInMinutes($reset->created_at) > 60) {
            $message = 'This password reset link is invalid or expired. Please request a new one.';
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => $message], 400);
            }
            return back()->withErrors(['email' => $message])->withInput();
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            $message = 'This password reset link is invalid or expired. Please request a new one.';
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => $message], 400);
            }
            return back()->withErrors(['email' => $message])->withInput();
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // The app uses database sessions by default. Clear existing sessions
        // for this account so a forgotten/compromised session is not left alive.
        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)
                ->delete();
        }

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        $message = 'Password reset successfully. Please sign in again.';
        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => $message]);
        }

        return redirect()->route('login')->with('success', $message);
    }
}
