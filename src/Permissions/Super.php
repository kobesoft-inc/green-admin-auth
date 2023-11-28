<?php

namespace Green\AdminAuth\Permissions;

use Green\AdminAuth\Models\AdminUser;
use Green\AdminAuth\Permissions\Permission;
use Illuminate\Support\Facades\Gate;

class Super extends Permission
{
    /**
     * パーミッションのグループ名
     *
     * @return string
     */
    static public function getGroup(): string
    {
        return __('green::admin_base.permissions.admin.group');
    }

    /**
     * パーミッションの表示名
     *
     * @return string
     */
    static public function getLabel(): string
    {
        return __('green::admin_base.permissions.admin.super');
    }
}