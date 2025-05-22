<?php

namespace Green\AdminAuth\Models\Group\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface ShouldHaveUsers
{
    public function users(): BelongsToMany;
}
