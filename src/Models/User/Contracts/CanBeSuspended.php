<?php

namespace Green\AdminAuth\Models\User\Contracts;

use Illuminate\Database\Eloquent\Casts\Attribute;

interface CanBeSuspended
{
    public function suspend(): void;

    public function resume(): void;

    public function isActive(): Attribute;
}
