<?php

namespace Green\AdminBase\Permissions;

use Green\AdminBase\Models\AdminUser;
use Green\AdminBase\Permissions\Permission;
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