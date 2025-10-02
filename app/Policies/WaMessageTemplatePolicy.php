<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WaMessageTemplate;
use Illuminate\Auth\Access\Response;

class WaMessageTemplatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WaMessageTemplate $waMessageTemplate): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'superadmin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WaMessageTemplate $waMessageTemplate): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WaMessageTemplate $waMessageTemplate): bool
    {
        return $user->role === 'superadmin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WaMessageTemplate $waMessageTemplate): bool
    {
        return $user->role === 'superadmin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WaMessageTemplate $waMessageTemplate): bool
    {
        return $user->role === 'superadmin';
    }
}
