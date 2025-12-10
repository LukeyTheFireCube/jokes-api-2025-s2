<?php

namespace App\Http\Controllers\Api\v2;


use App\Http\Controllers\Controller;
use App\Models\User;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    /**
     * GET /api/v2/users
     * List users with search + pagination
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:50'],
            'perPage' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $search = $validated['search'] ?? '';
        $perPage = $validated['perPage'] ?? 25;

        $users = User::query()
            ->when($search, fn ($q) =>
            $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
            )
            ->with('roles')
            ->paginate($perPage);

        return ApiResponse::success($users, "Users retrieved");
    }

    /**
     * POST /api/v2/users
     * Create a new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:64'],
            'email'    => ['required', 'email', 'max:128', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', 'string', Rule::in(['client', 'staff', 'admin', 'super-user'])],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status'   => 'active',
        ]);

        $user->syncRoles($validated['role']);

        return ApiResponse::success($user->load('roles'), "User created successfully", 201);
    }

    /**
     * GET /api/v2/users/{user}
     * View user details
     */
    public function show(User $user)
    {
        return ApiResponse::success($user->load('roles'), "User retrieved");
    }

    /**
     * PATCH /api/v2/users/{user}
     * Update user details + role
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['sometimes', 'required', 'string', 'max:64'],
            'email'    => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'role'     => ['sometimes', 'required', Rule::in(['client', 'staff', 'admin', 'super-user'])],
        ]);

        $user->update([
            'name'     => $validated['name'] ?? $user->name,
            'email'    => $validated['email'] ?? $user->email,
            'password' => isset($validated['password']) ? Hash::make($validated['password']) : $user->password,
        ]);

        if (isset($validated['role'])) {
            Gate::authorize('editRole', $user);
            $user->syncRoles($validated['role']);
        }

        return ApiResponse::success($user->load('roles'), "User updated");
    }

    /**
     * DELETE /api/v2/users/{user}
     * Delete a user
     */
    public function destroy(User $user)
    {
        $user->delete();

        return ApiResponse::success(null, 'User deleted successfully');
    }

    /**
     * POST /api/v2/users/{user}/force-logout
     */
    public function forceLogout(User $user)
    {
        Gate::authorize('forceLogout', $user);

        $user->remember_token = null;
        $user->save();

        return ApiResponse::success(null, 'User force-logged-out');
    }

    /**
     * POST /api/v2/users/{user}/status
     * Ban / suspend / activate
     */
    public function updateStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['active', 'suspended', 'banned']),
            ],
        ]);

        Gate::authorize('editStatus', $user);

        $user->status = $validated['status'];
        $user->save();

        return ApiResponse::success($user, "User status updated to {$user->status}");
    }
}
