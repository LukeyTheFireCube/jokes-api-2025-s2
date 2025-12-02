<?php

namespace App\Http\Controllers\Api\v2;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    /**
     * GET /api/admin/users
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

        return response()->json([
            'data' => $users,
        ]);
    }

    /**
     * POST /api/admin/users
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

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user->load('roles'),
        ], 201);
    }

    /**
     * GET /api/admin/users/{user}
     * View user details
     */
    public function show(User $user)
    {
        return response()->json([
            'data' => $user->load('roles'),
        ]);
    }

    /**
     * PATCH /api/admin/users/{user}
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

        return response()->json([
            'message' => 'User updated',
            'data' => $user->load('roles'),
        ]);
    }

    /**
     * DELETE /api/admin/users/{user}
     * Delete a user
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * POST /api/admin/users/{user}/force-logout
     */
    public function forceLogout(User $user)
    {
        Gate::authorize('forceLogout', $user);

        $user->remember_token = null;
        $user->save();

        return response()->json([
            'message' => 'User force-logged-out',
        ]);
    }

    /**
     * POST /api/admin/users/{user}/status
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

        return response()->json([
            'message' => "User status updated to {$user->status}",
            'data' => $user,
        ]);
    }
}
