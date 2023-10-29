<?php

namespace Green\AdminBase\Permissions;

use Green\AdminBase\Permissions\Permission;

class DeleteAdminUser extends Permission
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
        return __('green::admin_base.permissions.admin.delete_admin_user');
    }
}