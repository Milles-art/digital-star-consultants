<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class AdminLoginController extends Controller
{
    public function show(): View|RedirectResponse
    {
        // Do not automatically redirect an already-authenticated management
        // user back to the dashboard. If the dashboard has a runtime error,
        // that redirect traps the user in the broken page and makes it
        // impossible to reach the login/logout controls. The login page can
        // safely render for an authenticated user and provides a POST logout
        // action instead.
        if (Auth::check()) {
            if (! Auth::user()->isManagement()) {
                return redirect()->route('login');
            }

            return view('auth.admin-login', [
                'alreadyAuthenticated' => true,
            ]);
        }

        return view('auth.admin-login', [
            'alreadyAuthenticated' => false,
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $email = strtolower(trim($credentials['email']));
        $key = 'admin-login|'.$email.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Try again in {$seconds} seconds.",
            ]);
        }

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => 'The management credentials are incorrect.',
            ]);
        }

        if (! $user->isActive()) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => 'This account is currently deactivated.',
            ]);
        }

        if (! $user->isManagement()) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => 'This portal is restricted to management accounts.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        RateLimiter::clear($key);

        // Login must never fail just because the optional audit timestamp
        // cannot be written on an older local database.
        try {
            $user->forceFill(['last_login_at' => now()])->saveQuietly();
        } catch (Throwable $e) {
            Log::warning('Management login succeeded but last_login_at could not be saved.', [
                'user_id' => $user->id,
                'exception' => $e->getMessage(),
            ]);
        }

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', 'Welcome back, '.$user->name.'.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
