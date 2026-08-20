<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendWelcomeEmailJob;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View|JsonResponse
    {
        if (! request()->expectsJson()) {
            return view('auth.register');
        }

        return response()->json([
            'message' => 'Registration endpoint. Send POST request with name and email.',
        ]);
    }

    /**
     * Public self-registration creates a staff account only.
     * Role and is_active are set explicitly (not mass-assigned).
     */
    public function register(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        $tempPassword = Str::password(12);

        // Explicit assignment – role / is_active are not fillable
        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($tempPassword);
        $user->role = User::ROLE_STAFF;
        $user->is_active = true;
        $user->save();

        SendWelcomeEmailJob::dispatch($user, $tempPassword);

        if (! $request->expectsJson()) {
            return redirect()
                ->route('login')
                ->with('success', 'Your account was created. Check your email for the temporary password.');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Account created successfully. A welcome email with your temporary password has been sent.',
            'data' => [
                'user' => $user->only(['id', 'name', 'email', 'role', 'is_active']),
            ],
        ], 201);
    }
}
