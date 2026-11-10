<?php

namespace App\Policies;

/** Users, roles and site settings: Admin only. */
class AdminOnlyPolicy extends RoleGatedPolicy
{
    protected array $roles = ['Admin'];
}
