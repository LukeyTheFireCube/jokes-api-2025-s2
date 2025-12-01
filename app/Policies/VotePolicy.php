<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vote;
use App\Models\Joke;

class VotePolicy
{
    public function viewAny(User $user) { return true; } // optional
    public function view(User $user, Vote $vote) { return true; }
    /**
     * Determine whether the user can add a vote for a joke.
     */
    public function create(User $user): bool
    {
        return $user->can('vote.add');
    }

    /**
     * Determine whether the user can edit their own vote.
     */
    public function update(User $user, Vote $vote): bool
    {
        return $user->can('vote.edit');
    }

    /**
     * Determine whether the user can remove their own vote.
     */
    public function delete(User $user, Vote $vote): bool
    {
        return $user->can('vote.delete');
    }

    /**
     * Determine whether the user can clear all votes by another user.
     */
    public function clearAll(User $user): bool
    {
        return $user->can('vote.clearall');
    }

    /**
     * Determine whether the user can backup vote data.
     */
    public function backup(User $user): bool
    {
        return $user->hasRole('super-user');
    }
}

