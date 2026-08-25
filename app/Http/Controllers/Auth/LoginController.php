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
    public function showLoginForm(): View|JsonResponse|RedirectResponse
    {
        if (auth()->check()) {
            return redirect($this->getRedirectPath(auth()->user()));
        }

        if (! request()->expectsJson()) {
            return view('auth.login');
        }

        return response()->json([
            'message' => 'Login endpoint. Send POST request with email and password.',
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        $this->ensureIsNotRateLimited($request);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($this->throttleKey($request));

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (! $user->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account is deactivated. Please contact admin.',
            ], 403);
        }

        RateLimiter::clear($this->throttleKey($request));

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now()])->save();

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
                'redirect' => $this->getRedirectPath($user),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (! $request->expectsJson()) {
            return redirect()->route('login');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
            'redirect' => '/login',
        ]);
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
        return strtolower($request->input('email')) . '|' . $request->ip();
    }

    private function getRedirectPath(User $user): string
    {
        return $user->isManagement() ? '/admin/dashboard' : '/staff/submissions';
    }
}
