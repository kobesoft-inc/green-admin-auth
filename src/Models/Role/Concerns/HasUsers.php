<?php

namespace Green\AdminAuth\Models\Role\Concerns;

use Green\AdminAuth\Models\Concerns\GuessesRelated;
use Green\AdminAuth\Models\Role\Contracts\ShouldHaveUsers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * ロールを割り当てられたユーザを取得する
 *
 * @mixin Model|ShouldHaveUsers
 */
trait HasUsers
{
    use GuessesRelated;

    /**
     * 起動時の処理
     *
     * @return void
     */
    public static function bootHasUsers(): void
    {
        static::deleting(function (Model|ShouldHaveUsers $model) {
            $model->users()->detach();
        });
    }

    /**
     * ロールを割り当てられたユーザ
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            static::userClass(),
            static::userRolePivotTable(),
            static::roleForeignKey(),
            static::userForeignKey()
        );
    }
}
