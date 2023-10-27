<?php

namespace Green\AdminBase\Policies;

use Green\AdminBase\Models\AdminUser;
use Green\AdminBase\Permissions\ManageAdminRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminRolePolicy
{
    use HandlesAuthorization;

    /**
     * ロールの一覧表示ができるか？
     *
     * @param  AdminUser  $user
     * @return bool
     */
    public function viewAny(AdminUser $user): bool
    {
        return $user->hasPermission(ManageAdminRole::class);
    }
}