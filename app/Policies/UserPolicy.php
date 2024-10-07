<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can approve authors.
     */
    public function approveAuthor(User $user)
    {
        return $user->hasRole('admin') || $user->hasRole('editor');
    }

    /**
     * Determine whether the user can approve editors.
     */
    public function approveEditor(User $user)
    {
        return $user->hasRole('admin');
    }
}