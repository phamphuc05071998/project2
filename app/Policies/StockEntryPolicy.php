<?php
// app/Policies/StockEntryPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\StockEntry;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockEntryPolicy
{
    use HandlesAuthorization;

    public function update(User $user, StockEntry $stockEntry)
    {
        return $user->id === $stockEntry->user_id || $user->hasRole(['manager', 'admin']);
    }

    public function delete(User $user, StockEntry $stockEntry)
    {
        return $user->id === $stockEntry->user_id || $user->hasRole(['manager', 'admin']);
    }

    public function approve(User $user, StockEntry $stockEntry)
    {
        return $user->hasRole(['manager', 'admin']);
    }
}
