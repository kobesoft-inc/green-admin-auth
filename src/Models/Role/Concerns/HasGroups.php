<?php

namespace Green\AdminAuth\Models\Role\Concerns;

use Green\AdminAuth\Models\Concerns\GuessesRelated;
use Green\AdminAuth\Models\Role\Contracts\ShouldHaveGroups;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * ロールを割り当てられたグループを取得する
 *
 * @mixin Model|ShouldHaveGroups
 */
trait HasGroups
{
    use GuessesRelated;

    /**
     * 起動時の処理
     *
     * @return void
     */
    public static function bootHasGroups(): void
    {
        static::deleting(function (Model|ShouldHaveGroups $model) {
            $model->groups()->detach();
        });
    }

    /**
     * ロールを割り当てられたグループ
     *
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            static::roleClass(),
            static::groupRolePivotTable(),
            static::roleForeignKey(),
            static::groupForeignKey()
        );
    }
}
