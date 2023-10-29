<?php

namespace Green\AdminBase\Policies;

use Green\AdminBase\Models\AdminUser;
use Green\AdminBase\Permissions\DeleteAdminUser;
use Green\AdminBase\Permissions\ManageAdminUser;
use Green\AdminBase\Permissions\ManageAdminUserInGroup;
use Green\AdminBase\Permissions\ResetAdminUserPassword;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminUserPolicy
{
    use HandlesAuthorization;

    /**
     * ユーザーの一覧表示ができるか？
     *
     * @param  AdminUser  $user  作業者のユーザー
     * @return bool
     */
    public function viewAny(AdminUser $user): bool
    {
        return $user->hasPermission(ManageAdminUser::class)
            || $user->hasPermission(ManageAdminUserInGroup::class);
    }

    /**
     * ユーザーの削除ができるか？
     *
     * @param  AdminUser  $user  作業者のユーザー
     * @param  AdminUser  $model  対象のユーザー
     * @return bool
     */
    public function delete(AdminUser $user, AdminUser $model): bool
    {
        // 自分自身は削除できない。削除権限を持っていること
        return $user->id != $model->id
            && $user->hasPermission(DeleteAdminUser::class);
    }

    /**
     * ユーザーの停止ができるか？
     *
     * @param  AdminUser  $user  作業者のユーザー
     * @param  AdminUser  $model  対象のユーザー
     * @return bool
     */
    public function suspend(AdminUser $user, AdminUser $model): bool
    {
        // 自分自身は停止できない
        return $user->id != $model->id;
    }

    /**
     * ユーザーのパスワードリセットができるか？
     *
     * @param  AdminUser  $user  作業者のユーザー
     * @param  AdminUser  $model  対象のユーザー
     * @return bool
     */
    public function resetPassword(AdminUser $user, AdminUser $model): bool
    {
        return $user->hasPermission(ResetAdminUserPassword::class);
    }
}