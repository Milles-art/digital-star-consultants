<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the login form in the browser, or a short JSON hint for API clients.
     */
    public function showLoginForm(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Login endpoint. Send POST request with email and password.',
            ]);
        }

        return view('auth.login');
    }

    /**
     * Authenticate the user (browser redirect or JSON for API / AJAX).
     */
    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $this->ensureIsNotRateLimited($request);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($this->throttleKey($request));

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials',
                ], 401);
            }

            throw ValidationException::withMessages([
                'email' => 'Invalid credentials.',
            ]);
        }

        if (! $user->is_active) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your account is deactivated. Please contact admin.',
                ], 403);
            }

            throw ValidationException::withMessages([
                'email' => 'Your account is deactivated. Please contact admin.',
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->save();

        $redirect = $this->getRedirectPath($user);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Logged in successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'role_label' => $user->role_label ?? null,
                    ],
                    'redirect' => $redirect,
                ],
            ]);
        }

        return redirect()->intended($redirect);
    }

    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully',
                'redirect' => '/login',
            ]);
        }

        return redirect()->route('login');
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->status(429);
        }
    }

    protected function throttleKey(Request $request): string
    {
        return strtolower((string) $request->input('email')).'|'.$request->ip();
    }

    private function getRedirectPath(User $user): string
    {
        return $user->isManagement()
            ? '/admin/dashboard'
            : '/staff/submissions';
    }
}
