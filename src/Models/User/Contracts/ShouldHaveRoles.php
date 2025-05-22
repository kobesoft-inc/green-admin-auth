<?php

namespace Green\AdminAuth\Models\User\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface ShouldHaveRoles
{
    public function roles(): BelongsToMany;
}
