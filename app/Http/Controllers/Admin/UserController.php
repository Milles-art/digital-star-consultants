<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendWelcomeEmailJob;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate($request->integer('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $users,
        ]);
    }

    public function stats(): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'by_role' => User::selectRaw('role, count(*) as count')
                    ->groupBy('role')
                    ->pluck('count', 'role'),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'nullable|in:admin,ceo,gm,staff',
        ]);

        $tempPassword = Str::password(12);

        // Explicit assignment — role is NOT mass-assignable
        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($tempPassword);
        $user->role = $validated['role'] ?? User::ROLE_STAFF;
        $user->is_active = true;
        $user->save();

        SendWelcomeEmailJob::dispatch($user, $tempPassword);

        // Never return the temporary password in the response
        return response()->json([
            'status' => 'success',
            'message' => 'Staff user created successfully. A welcome email with temporary password has been sent.',
            'data' => [
                'user' => $user->only(['id', 'name', 'email', 'role', 'is_active', 'created_at']),
            ],
        ], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'role' => 'nullable|in:admin,ceo,gm,staff',
            'is_active' => 'nullable|boolean',
        ]);

        // Explicit assignment of privileged fields
        if (array_key_exists('name', $validated) && $validated['name'] !== null) {
            $user->name = $validated['name'];
        }
        if (array_key_exists('email', $validated) && $validated['email'] !== null) {
            $user->email = $validated['email'];
        }
        if (array_key_exists('role', $validated) && $validated['role'] !== null) {
            $user->role = $validated['role'];
        }
        if (array_key_exists('is_active', $validated) && $validated['is_active'] !== null) {
            $user->is_active = $validated['is_active'];
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user->only(['id', 'name', 'email', 'role', 'is_active', 'updated_at']),
        ]);
    }

    public function toggleActive(User $user): JsonResponse
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

    public function destroy(User $user): JsonResponse
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
