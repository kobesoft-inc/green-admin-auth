<?php

namespace Green\AdminAuth\Models\User\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface ShouldLogLogin
{
    public function loginLogs(): HasMany;
}
