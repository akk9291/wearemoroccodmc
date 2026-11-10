<?php

namespace App\Policies;

use App\Models\User;

/**
 * Every ability is decided by role membership alone, in before(). Subclasses list the roles
 * that may manage the model. The ability methods exist because Filament checks method_exists()
 * (a missing method would mean "allowed"); they are only reached when before() returns null.
 */
abstract class RoleGatedPolicy
{
    /** @var list<string> */
    protected array $roles = ['Admin'];

    /** Abilities nobody has (e.g. creating lead submissions by hand). */
    protected array $denied = [];

    public function before(User $user, string $ability): ?bool
    {
        if (in_array($ability, $this->denied, true)) {
            return false;
        }

        return $user->hasAnyRole($this->roles);
    }

    public function viewAny(User $user): bool { return false; }

    public function view(User $user, $model): bool { return false; }

    public function create(User $user): bool { return false; }

    public function update(User $user, $model): bool { return false; }

    public function delete(User $user, $model): bool { return false; }

    public function deleteAny(User $user): bool { return false; }

    public function restore(User $user, $model): bool { return false; }

    public function restoreAny(User $user): bool { return false; }

    public function forceDelete(User $user, $model): bool { return false; }

    public function forceDeleteAny(User $user): bool { return false; }

    public function replicate(User $user, $model): bool { return false; }

    public function reorder(User $user): bool { return false; }
}
