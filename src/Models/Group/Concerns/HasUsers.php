<?php

namespace Green\AdminAuth\Models\Group\Concerns;

use Green\AdminAuth\Models\Concerns\GuessesRelated;
use Green\AdminAuth\Models\Group\Contracts\ShouldHaveUsers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * グループはユーザーを持っている
 *
 * @mixin Model|GuessesRelated
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
     * このグループに所属するユーザー
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            static::userClass(),
            static::userGroupPivotTable(),
            static::groupForeignKey(),
            static::userForeignKey()
        );
    }
}
