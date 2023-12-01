<?php

namespace Green\AdminAuth\Permissions;

/**
 * ロールを管理する権限
 */
class ManageAdminRole extends Permission
{
    /**
     * パーミッションのグループ名
     *
     * @return string
     */
    static public function getGroup(): string
    {
        return __('green::admin-auth.permissions.admin.group');
    }

    /**
     * パーミッションの表示名
     *
     * @return string
     */
    static public function getLabel(): string
    {
        return __('green::admin-auth.permissions.admin.manage-admin-role');
    }
}
