<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function forceLogout(User $authUser, User $targetUser)
    {
        if ($authUser->hasRole('super-user')) {
            return true;
        }

        if ($authUser->hasRole('admin')) {
            return !$targetUser->hasRole('admin') && !$targetUser->hasRole('super-user');
        }

        if ($authUser->hasRole('staff')) {
            return $targetUser->hasRole('client');
        }

        if ($authUser->hasRole('client')) {
            return $authUser->id === $targetUser->id;
        }

        return false;
    }
}
