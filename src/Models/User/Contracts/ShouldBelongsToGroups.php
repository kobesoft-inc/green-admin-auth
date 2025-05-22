<?php

namespace Green\AdminAuth\Models\User\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface ShouldBelongsToGroups
{
    public function scopeInGroups(\Illuminate\Database\Eloquent\Builder $query, \Illuminate\Support\Collection $groups): void;

    public function groupsWithDescendants(): \Illuminate\Support\Collection;

    public function groups(): BelongsToMany;
}
