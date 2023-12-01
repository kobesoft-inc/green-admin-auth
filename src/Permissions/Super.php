<?php

namespace Green\AdminAuth\Permissions;

/**
 * 特権の権限
 */
class Super extends Permission
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
        return __('green::admin-auth.permissions.admin.super');
    }
}
