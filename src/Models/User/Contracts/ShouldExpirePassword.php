<?php

namespace Green\AdminAuth\Models\User\Contracts;

interface ShouldExpirePassword
{
    function passwordExpiresAtColumn();

    function isPasswordExpired();

    function defaultPasswordValidityInDays(): ?int;
}
