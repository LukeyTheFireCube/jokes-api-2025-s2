<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    //
    public function index(): View
    {
        $userCount = User::count();
        $userSuspendedCount = User::where('status', 'suspended')->count();
        $userBannedCount = User::where('status', 'banned')->count();

        return view('admin.index')
            ->with('userCount', Number::format($userCount))
            ->with('userSuspendedCount', Number::format($userSuspendedCount));
    }

    //
    public function users(): View
    {
        $users = User::paginate(10);

        return view('admin.users.index')
            ->with('users', $users);
    }

    public function forceLogout(User $user)
    {
        Gate::authorize('forceLogout', $user->load('roles'));

        DB::table('users')->where('id', $user->id)->update(['remember_token' => null]);

        return response()->json(['message' => 'User logged out successfully']);
    }
}
