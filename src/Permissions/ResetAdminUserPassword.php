<?php

namespace Green\AdminAuth\Permissions;

use Green\AdminAuth\Permissions\Permission;

class ResetAdminUserPassword extends Permission
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
        return __('green::admin_base.permissions.admin.reset_admin_user_password');
    }
}