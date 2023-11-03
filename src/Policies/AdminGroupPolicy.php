<?php

namespace Green\AdminBase\Policies;

use Green\AdminBase\Models\AdminUser;
use Green\AdminBase\Permissions\ManageAdminGroup;
use Green\AdminBase\Plugin;
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