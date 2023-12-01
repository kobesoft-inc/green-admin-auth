<?php

namespace Green\AdminAuth\Policies;

use Green\AdminAuth\Models\AdminUser;
use Green\AdminAuth\Permissions\ManageAdminRole;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * ロールのポリシー
 *
 * @package Green\AdminAuth\Policies
 */
class AdminRolePolicy
{
    use HandlesAuthorization;

    /**
     * ロールの一覧表示ができるか？
     *
     * @param AdminUser $user
     * @return bool
     */
    public function viewAny(AdminUser $user): bool
    {
        return $user->hasPermission(ManageAdminRole::class);
    }
}
