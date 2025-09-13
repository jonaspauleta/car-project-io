<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Car;
use App\Models\Modification;
use App\Models\User;

class ModificationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Car $car): bool
    {
        return $user->id === $car->user_id; // Users can view their own modifications
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Modification $modification): bool
    {
        return $user->id === $modification->car->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Car $car): bool
    {
        return $user->id === $car->user_id; // Users can create modifications for their own cars
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Modification $modification): bool
    {
        return $user->id === $modification->car->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Modification $modification): bool
    {
        return $user->id === $modification->car->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Modification $modification): bool
    {
        return $user->id === $modification->car->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Modification $modification): bool
    {
        return $user->id === $modification->car->user_id;
    }
}
