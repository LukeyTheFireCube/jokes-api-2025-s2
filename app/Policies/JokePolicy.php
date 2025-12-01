<?php

namespace App\Policies;

use App\Models\Joke;
use App\Models\User;

class JokePolicy
{
    /**
     * Helpers
     */
    private function isClient(User $user): bool
    {
        return $user->hasRole('client');
    }

    private function isStaff(User $user): bool
    {
        return $user->hasRole('staff');
    }

    private function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    private function isSuper(User $user): bool
    {
        return $user->hasRole('super-user');
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // No access for unregistered
        if (!$user) {
            return false;
        }

        return $this->isStaff($user) || $this->isAdmin($user) || $this->isSuper($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Joke $joke): bool
    {
        // No access for unregistered
        if (!$user) {
            return false;
        }

        // Client can see only their own jokes
        if ($this->isClient($user)) {
            return $joke->user_id === $user->id;
        }

        // Higher-ups can view everything
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Clients cannot add
        if ($this->isClient($user)) {
            return false;
        }

        // Staff can add jokes for clients
        if ($this->isStaff($user)) {
            return true;
        }

        // Admin and Super can create anything
        return $this->isAdmin($user) || $this->isSuper($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Joke $joke): bool
    {
        // Client can only update their own joke
        if ($this->isClient($user)) {
            return $joke->user_id === $user->id;
        }

        // Staff can update clients' jokes
        if ($this->isStaff($user)) {
            return $joke->user?->hasRole('client');
        }

        // Admin can update jokes of staff or client
        if ($this->isAdmin($user)) {
            return $joke->user?->hasAnyRole(['client', 'staff']);
        }

        // Super-user can update all
        return $this->isSuper($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Joke $joke): bool
    {
        // Client can only delete their own
        if ($this->isClient($user)) {
            return $joke->user_id === $user->id;
        }

        // Staff can delete clients' jokes
        if ($this->isStaff($user)) {
            return $joke->user?->hasRole('client');
        }

        // Admin can delete jokes of clients or staff
        if ($this->isAdmin($user)) {
            return $joke->user?->hasAnyRole(['client', 'staff']);
        }

        // Super-user can delete any joke except their own
        if ($this->isSuper($user)) {
            return $joke->user_id !== $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Joke $joke): bool
    {
        // Same rules as delete
        return $this->delete($user, $joke);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Joke $joke): bool
    {
        // Same rules as delete
        return $this->delete($user, $joke);
    }
}
