<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (! request()->expectsJson()) {
            return view('auth.login');
        }

        return response()->json([
            'message' => 'Login endpoint. Send POST request with email and password.',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        // Find user
        $user = User::where('email', $request->email)->first();

        // Check if user exists
        if (! $user) {
            if (! $request->expectsJson()) {
                return back()->withInput($request->only('email'))->withErrors(['email' => 'These credentials do not match our records.']);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Check if user is active
        if (! $user->is_active) {
            if (! $request->expectsJson()) {
                return back()->withInput($request->only('email'))->withErrors(['email' => 'Your account is deactivated. Please contact an administrator.']);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Your account is deactivated. Please contact admin.',
            ], 403);
        }

        // Check password
        if (! Hash::check($request->password, $user->password)) {
            if (! $request->expectsJson()) {
                return back()->withInput($request->only('email'))->withErrors(['email' => 'These credentials do not match our records.']);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Login user
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Determine redirect based on role
        $redirect = $this->getRedirectPath($user);

        if (! $request->expectsJson()) {
            return redirect()->intended($redirect);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logged in successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'role_label' => $user->role_label,
                ],
                'redirect' => $redirect,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (! $request->expectsJson()) {
            return redirect()->route('login')->with('success', 'You have been logged out.');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
            'redirect' => '/login',
        ]);
    }

    private function getRedirectPath(User $user): string
    {
        if ($user->isManagement()) {
            return '/admin/dashboard';
        }

        return '/staff/submissions';
    }
}
