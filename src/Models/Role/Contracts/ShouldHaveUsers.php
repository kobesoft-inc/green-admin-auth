<?php

namespace Green\AdminAuth\Models\Role\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface ShouldHaveUsers
{
    public function users(): BelongsToMany;
}
