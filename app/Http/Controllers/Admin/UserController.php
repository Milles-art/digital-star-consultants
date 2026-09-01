<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Jobs\SendWelcomeEmailJob;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View|JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()->latest()->get();

        if (! request()->expectsJson()) {
            return view('admin.users.index', [
                'users' => $users,
                'stats' => [
                    'total' => User::count(),
                    'active' => User::where('is_active', true)->count(),
                    'inactive' => User::where('is_active', false)->count(),
                    'staff' => User::staff()->count(),
                    'management' => User::management()->count(),
                ],
                'roles' => [
                    User::ROLE_ADMIN => 'Administrator',
                    User::ROLE_CEO => 'CEO',
                    User::ROLE_GENERAL_MANAGER => 'General Manager',
                    User::ROLE_STAFF => 'Staff',
                ],
                'roleDescriptions' => [
                    User::ROLE_ADMIN => 'Full management access',
                    User::ROLE_CEO => 'Executive oversight',
                    User::ROLE_GENERAL_MANAGER => 'Operations management',
                    User::ROLE_STAFF => 'Process assigned requests',
                ],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => UserResource::collection($users),
        ]);
    }

    public function stats(): JsonResponse
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
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:admin,ceo,gm,staff'],
        ]);

        if (! $this->canAssignRole($request->user(), $data['role'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to assign that role.',
            ], 403);
        }

        $tempPassword = Str::password(16, true, true, false, false);

        $user = new User();
        $user->name = trim($data['name']);
        $user->email = strtolower(trim($data['email']));
        $user->role = $data['role'];
        $user->is_active = true;
        $user->password = Hash::make($tempPassword);
        $user->save();

        SendWelcomeEmailJob::dispatch($user, $tempPassword);

        return response()->json([
            'status' => 'success',
            'message' => 'Team member created successfully.',
            'credentials' => [
                'email' => $user->email,
                'temporary_password' => $tempPassword,
                'note' => 'The temporary password is shown once. The welcome email job has also been queued.',
            ],
            'data' => [
                'user' => new UserResource($user),
            ],
        ], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,ceo,gm,staff'],
            'is_active' => ['required', 'boolean'],
        ]);

        if ($user->id === $request->user()->id && ($data['role'] !== $user->role || (bool) $data['is_active'] !== (bool) $user->is_active)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot change your own role or deactivate your own account.',
            ], 422);
        }

        if (! $this->canManageTarget($request->user(), $user)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to manage this account.',
            ], 403);
        }

        if (! $this->canAssignRole($request->user(), $data['role'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to assign that role.',
            ], 403);
        }

        $user->update([
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'role' => $data['role'],
            'is_active' => (bool) $data['is_active'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Team member updated successfully.',
            'data' => new UserResource($user->fresh()),
        ]);
    }

    public function toggleActive(Request $request, User $user): JsonResponse
    {
        $this->authorize('toggleActive', $user);

        if (! $this->canManageTarget($request->user(), $user)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to change this account.',
            ], 403);
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => $user->is_active ? 'User activated.' : 'User deactivated.',
            'data' => new UserResource($user->fresh()),
        ]);
    }

    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        if (! $this->canManageTarget($request->user(), $user)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to reset this account.',
            ], 403);
        }

        $temporaryPassword = Str::password(18, true, true, false, false);
        $user->forceFill(['password' => Hash::make($temporaryPassword)])->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successfully.',
            'credentials' => [
                'email' => $user->email,
                'temporary_password' => $temporaryPassword,
                'note' => 'This password is shown once. Share it securely with the team member.',
            ],
        ]);
    }

    public function workload(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => Submission::where('processed_by', $user->id)->count(),
                'open' => Submission::where('processed_by', $user->id)
                    ->whereNotIn('status', ['completed', 'rejected', 'cancelled'])->count(),
                'completed' => Submission::where('processed_by', $user->id)->where('status', 'completed')->count(),
            ],
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        if (! $this->canManageTarget($request->user(), $user)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to delete this account.',
            ], 403);
        }

        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot delete your own account.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully.',
        ]);
    }
    private function canManageTarget(User $actor, User $target): bool
    {
        if ($actor->isAdmin()) {
            return true;
        }

        return $target->role === User::ROLE_STAFF;
    }

    private function canAssignRole(User $actor, string $role): bool
    {
        return $actor->isAdmin() || $role === User::ROLE_STAFF;
    }

}
