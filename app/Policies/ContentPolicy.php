<?php

namespace App\Policies;

/** Website content and menus: Admin + Editor. */
class ContentPolicy extends RoleGatedPolicy
{
    protected array $roles = ['Admin', 'Editor'];
}
