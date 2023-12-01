<?php

namespace Green\AdminAuth\Policies;

use Green\AdminAuth\Models\AdminUser;
use Green\AdminAuth\Permissions\DeleteAdminUser;
use Green\AdminAuth\Permissions\ManageAdminUser;
use Green\AdminAuth\Permissions\ManageAdminUserInGroup;
use Green\AdminAuth\Permissions\ResetAdminUserPassword;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * 管理ユーザーのポリシー
 *
 * @package Green\AdminAuth\Policies
 */
class AdminUserPolicy
{
    use HandlesAuthorization;

    /**
     * 管理ユーザーの一覧表示ができるか？
     *
     * @param AdminUser $user 作業者のユーザー
     * @return bool
     */
    public function viewAny(AdminUser $user): bool
    {
        return $user->hasPermission(ManageAdminUser::class)
            || $user->hasPermission(ManageAdminUserInGroup::class);
    }

    /**
     * 管理ユーザーの削除ができるか？
     *
     * @param AdminUser $user 作業者のユーザー
     * @param AdminUser $model 対象のユーザー
     * @return bool
     */
    public function delete(AdminUser $user, AdminUser $model): bool
    {
        // 自分自身は削除できない。削除権限を持っていること
        return $user->id != $model->id
            && $user->hasPermission(DeleteAdminUser::class);
    }

    /**
     * 管理ユーザーの停止ができるか？
     *
     * @param AdminUser $user 作業者のユーザー
     * @param AdminUser $model 対象のユーザー
     * @return bool
     */
    public function suspend(AdminUser $user, AdminUser $model): bool
    {
        // 自分自身は停止できない
        return $user->id != $model->id;
    }

    /**
     * 管理ユーザーのパスワードリセットができるか？
     *
     * @param AdminUser $user 作業者のユーザー
     * @param AdminUser $model 対象のユーザー
     * @return bool
     */
    public function resetPassword(AdminUser $user, AdminUser $model): bool
    {
        return $user->hasPermission(ResetAdminUserPassword::class);
    }
}
