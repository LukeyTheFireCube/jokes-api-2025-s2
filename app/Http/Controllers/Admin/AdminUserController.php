<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'perPage' => ['integer', 'nullable'],
            'page' => ['integer', 'nullable'],
            'search' => ['nullable', 'string', 'max:32'],
        ]);

        $perPage = $validated['perPage'] ?? 12;
        $page = $validated['page'] ?? 1;
        $search = $validated['search'] ?? '';

        $users = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            })
            ->with('roles')
            ->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page);

        return view('admin.users.index')
            ->with('users', $users)
            ->with('search', $search);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('admin.users.create')
            ->with('roles', Role::all());
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:32'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['nullable', 'string', Rule::exists('roles', 'name')],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        if (!empty($validated['role'])) {
            $user->assignRole($validated['role']);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} created successfully");
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        return view('admin.users.show')
            ->with('user', $user->load('roles'));
    }

    /**
     * Show the form for editing the user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit')
            ->with('user', $user)
            ->with('roles', Role::all());
    }

    /**
     * Update the user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'given_name' => ['required', 'string', 'min:2', 'max:32'],
            'family_name' => ['required', 'string', 'min:2', 'max:32'],
            'email' => [
                'required', 'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['nullable', 'string', Rule::exists('roles', 'name')],
        ]);

        $data = [
            'given_name' => $validated['given_name'],
            'family_name' => $validated['family_name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        $user->update($data);

        if (!empty($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} updated successfully");
    }

    /**
     * Confirm deletion.
     */
    public function delete(User $user): View
    {
        return view('admin.users.delete')
            ->with('user', $user);
    }

    /**
     * Delete the user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $name = $user->name;

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User {$name} deleted successfully");
    }
}

