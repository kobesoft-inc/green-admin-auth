<?php

namespace Green\AdminAuth\Policies;

use Green\AdminAuth\Models\AdminUser;
use Green\AdminAuth\Permissions\ManageAdminGroup;
use Green\AdminAuth\GreenAdminAuthPlugin;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * グループのポリシー
 *
 * @package Green\AdminAuth\Policies
 */
class AdminGroupPolicy
{
    use HandlesAuthorization;

    /**
     * グループの一覧表示ができるか？
     *
     * @param AdminUser $user
     * @return bool
     */
    public function viewAny(AdminUser $user): bool
    {
        return $user->hasPermission(ManageAdminGroup::class)
            && !GreenAdminAuthPlugin::get()->isGroupDisabled();
    }
}
