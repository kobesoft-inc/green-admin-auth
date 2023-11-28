<?php

namespace Green\AdminAuth\Policies;

use Green\AdminAuth\Models\AdminUser;
use Green\AdminAuth\Permissions\ManageAdminGroup;
use Green\AdminAuth\Plugin;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminGroupPolicy
{
    use HandlesAuthorization;

    /**
     * グループの一覧表示ができるか？
     *
     * @param  AdminUser  $user
     * @return bool
     */
    public function viewAny(AdminUser $user): bool
    {
        return $user->hasPermission(ManageAdminGroup::class)
            && !Plugin::get()->isGroupDisabled();
    }
}