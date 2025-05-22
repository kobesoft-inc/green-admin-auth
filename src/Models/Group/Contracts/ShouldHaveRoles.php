<?php

namespace Green\AdminAuth\Models\Group\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface ShouldHaveRoles
{
    public function roles(): BelongsToMany;
}
