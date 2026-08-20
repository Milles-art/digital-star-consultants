<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendWelcomeEmailJob;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::latest()->get();

        if (! request()->expectsJson()) {
            return view('admin.users.index', compact('users'));
        }

        return response()->json([
            'status' => 'success',
            'data' => $users,
        ]);
    }

    public function stats()
    {
        $this->authorize('viewAny', User::class);

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => User::count(),
                'admin' => User::where('role', 'admin')->count(),
                'ceo' => User::where('role', 'ceo')->count(),
                'gm' => User::where('role', 'gm')->count(),
                'staff' => User::where('role', 'staff')->count(),
                'active' => User::where('is_active', true)->count(),
                'inactive' => User::where('is_active', false)->count(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'nullable|in:admin,ceo,gm,staff',
        ]);

        $tempPassword = Str::random(12);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($tempPassword),
            'role' => $request->role ?? User::ROLE_STAFF,
            'is_active' => true,
        ]);

        // Dispatch job to send welcome email
        SendWelcomeEmailJob::dispatch($user, $tempPassword);

        return response()->json([
            'status' => 'success',
            'message' => 'Staff user created successfully',
            'data' => [
                'user' => $user,
            ],
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,'.$user->id,
            'role' => 'nullable|in:admin,ceo,gm,staff',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'email', 'role', 'is_active']);

        $user->update(array_filter($data, function ($value) {
            return ! is_null($value);
        }));

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user,
        ]);
    }

    public function toggleActive(User $user)
    {
        $this->authorize('toggleActive', $user);

        $user->is_active = ! $user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'status' => 'success',
            'message' => "User {$status} successfully",
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'is_active' => $user->is_active,
            ],
        ]);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot delete your own account',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully',
        ]);
    }
}
