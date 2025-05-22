<?php

namespace Green\AdminAuth\Models\Role\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface ShouldHaveGroups
{
    public function groups(): BelongsToMany;
}
