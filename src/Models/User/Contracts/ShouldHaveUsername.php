<?php

namespace Green\AdminAuth\Models\User\Contracts;

/**
 * ユーザー名がある
 */
interface ShouldHaveUsername
{
    public static function getUsernameColumn(): string;

    public static function canLoginWithEmail(): bool;

    public static function canLoginWithUsername(): bool;
}
