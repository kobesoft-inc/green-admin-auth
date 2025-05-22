<?php

namespace Green\AdminAuth\Models\Group\Concerns;

use Green\AdminAuth\Models\Concerns\GuessesRelated;
use Green\AdminAuth\Models\Group\Contracts\ShouldHaveRoles;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * グループはロールを持っている
 *
 * @mixin Model|GuessesRelated
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
     * このグループに割り当てられたロール
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            static::roleClass(),
            static::groupRolePivotTable(),
            static::groupForeignKey(),
            static::roleForeignKey()
        );
    }

    /**
     * パーミッションを取得する
     *
     * @return Attribute
     */
    public function permissions(): Attribute
    {
        return Attribute::make(
            get: function () {
                $permissions = $this->parent ? $this->parent->permissions : collect();
                foreach ($this->roles as $role) {
                    $permissions = $permissions->concat($role->permissions);
                }
                return $permissions->unique();
            }
        )->shouldCache();
    }
}
