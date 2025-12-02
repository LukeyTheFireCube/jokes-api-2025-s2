<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Helper: Numeric role hierarchy
     */
    private function roleLevel(User $user): int
    {
        return match (true) {
            $user->hasRole('super-user') => 999,
            $user->hasRole('admin')      => 750,
            $user->hasRole('staff')      => 500,
            $user->hasRole('client')     => 100,
            default => 0,
        };
    }

    /**
     * Can browse users
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->can('user.browse');
    }

    /**
     * Can read a specific user
     */
    public function view(User $authUser, User $targetUser): bool
    {
        if ($authUser->can('user.read')) {
            return $this->roleLevel($authUser) >= $this->roleLevel($targetUser);
        }

        // Clients can read only themselves
        return $authUser->id === $targetUser->id;
    }

    /**
     * Can create users
     */
    public function create(User $authUser): bool
    {
        return $authUser->can('user.add');
    }

    /**
     * Can edit user
     */
    public function update(User $authUser, User $targetUser): bool
    {
        if (! $authUser->can('user.edit')) {
            return false;
        }

        // Users may edit only users with LOWER role level
        return $this->roleLevel($authUser) > $this->roleLevel($targetUser);
    }

    /**
     * Can delete user
     */
    public function delete(User $authUser, User $targetUser): bool
    {
        if (! $authUser->can('user.delete')) {
            return false;
        }

        // Must have higher role
        return $this->roleLevel($authUser) > $this->roleLevel($targetUser);
    }

    /**
     * Can update roles of another user
     */
    public function updateRole(User $authUser, User $targetUser): bool
    {
        if (! $authUser->hasRole(['admin', 'super-user'])) {
            return false;
        }

        // Must be higher in hierarchy
        return $this->roleLevel($authUser) > $this->roleLevel($targetUser);
    }
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
