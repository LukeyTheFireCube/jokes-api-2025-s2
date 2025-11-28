<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use App\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::all();
        return ApiResponse::success($roles, 200);
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'array'
        ]);

        $role = Role::create($validated);

        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return ApiResponse::success($role, 'Role created', 201);
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        if ($role->name === 'super-user') {
            return ApiResponse::error(null, "Super-user role cannot be modified", 403);
        }

        $validated = $request->validate([
            'name'        => 'required|string|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'array'
        ]);

        $role->update($validated);

        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return ApiResponse::success($role, 'Role updated');
    }

    public function destroy(Role $role): JsonResponse
    {
        if ($role->name === 'super-user') {
            return ApiResponse::error(null, "Super-user role cannot be deleted", 403);
        }

        $role->delete();

        return ApiResponse::success(null, 'Role deleted');
    }
}

