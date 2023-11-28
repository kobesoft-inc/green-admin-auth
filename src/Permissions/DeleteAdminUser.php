<?php

namespace Green\AdminAuth\Permissions;

use Green\AdminAuth\Permissions\Permission;
use Green\AdminAuth\Plugin;

class DeleteAdminUser extends Permission
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
        return __(
            'green::admin-auth.permissions.admin.delete-admin-user',
            Plugin::get()->getTranslationWords()
        );
    }
}
