<?php

namespace Green\AdminAuth\Models\User\Contracts;

use Illuminate\Database\Eloquent\Casts\Attribute;

interface ShouldHaveAvatar
{
    public function avatarUrl(): Attribute;
}
