<?php

namespace Green\AdminAuth\Models\User\Concerns;

use Green\AdminAuth\Models\Concerns\GuessesRelated;
use Green\AdminAuth\Models\User\Contracts\ShouldHaveRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Throwable;

/**
 * ユーザーにロールが割り当てられている
 *
 * @mixin Model
 */
trait HasRoles
{
    use GuessesRelated;

    /**
     * 起動時の処理
     *
     * @return void
     */
    public static function bootHasRoles(): void
    {
        static::deleting(function (Model|ShouldHaveRoles $model) {
            $model->roles()->detach();
        });
    }

    /**
     * この管理ユーザーに割り当てられたロール
     *
     * @return BelongsToMany
     * @throws Throwable
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            static::roleClass(),
            static::userRolePivotTable(),
            static::userForeignKey(),
            static::roleForeignKey()
        );
    }
}
