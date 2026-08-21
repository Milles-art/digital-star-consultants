<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Jobs\SendWelcomeEmailJob;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => UserResource::collection($users)
        ]);
    }

    public function stats()
    {
        $this->authorize('viewAny', User::class);

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => User::count(),
                'admin' => User::where('role', User::ROLE_ADMIN)->count(),
                'ceo' => User::where('role', User::ROLE_CEO)->count(),
                'gm' => User::where('role', User::ROLE_GENERAL_MANAGER)->count(),
                'staff' => User::where('role', User::ROLE_STAFF)->count(),
                'active' => User::where('is_active', true)->count(),
                'inactive' => User::where('is_active', false)->count(),
            ]
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

        $tempPassword = Str::random(16);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role ?? User::ROLE_STAFF;
        $user->is_active = true;
        $user->password = Hash::make($tempPassword);
        $user->save();

        SendWelcomeEmailJob::dispatch($user, $tempPassword);

        return response()->json([
            'status' => 'success',
            'message' => 'Staff user created successfully. A welcome email with login instructions has been sent.',
            'data' => [
                'user' => new UserResource($user),
            ]
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'role' => 'nullable|in:admin,ceo,gm,staff',
            'is_active' => 'nullable|boolean',
        ]);

        $user->update($request->only(['name', 'email', 'role', 'is_active']));

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => new UserResource($user)
        ]);
    }

    public function toggleActive(User $user)
    {
        $this->authorize('toggleActive', $user);

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'status' => 'success',
            'message' => "User {$status} successfully",
            'data' => new UserResource($user)
        ]);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot delete your own account'
            ], 422);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ]);
    }
}
