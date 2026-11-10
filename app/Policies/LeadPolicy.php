<?php

namespace App\Policies;

/** Form submissions: Admin + Sales. They come from the website, so nobody creates them by hand. */
class LeadPolicy extends RoleGatedPolicy
{
    protected array $roles = ['Admin', 'Sales'];

    protected array $denied = ['create', 'replicate'];
}
