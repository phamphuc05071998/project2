<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->hasPermissionTo('create posts');
    }

    public function update(User $user, Post $post)
    {
        return $user->hasPermissionTo('edit own posts') && $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post)
    {
        return $user->hasPermissionTo('delete own posts') && $user->id === $post->user_id;
    }

    public function approve(User $user)
    {
        return $user->hasPermissionTo('approve posts');
    }
    public function assignCategory(User $user)
    {
        return $user->hasPermissionTo('assign categories');
    }
}
