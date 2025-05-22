<?php

namespace Green\AdminAuth\Models\User\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * ユーザーが権限を持つことを示すインターフェース
 *
 * @property \Illuminate\Support\Collection $permissions
 * @mixin Model
 */
trait HasPermissions
{
    /**
     * パーミッションを取得する
     *
     * @return Attribute
     */
    public function permissions(): Attribute
    {
        return Attribute::make(
            get: function () {
                $permissions = collect();

                // ユーザーに関連するロールのパーミッションを取得
                if ($this instanceof \Green\AdminAuth\Models\User\Contracts\ShouldHaveRoles) {
                    $permissions = $this->roles->pluck('permissions')->flatten();
                }

                // ユーザーに関連するグループのパーミッションを取得
                if ($this instanceof \Green\AdminAuth\Models\User\Contracts\ShouldBelongsToGroups
                    && static::groupClass() instanceof \Green\AdminAuth\Models\Group\Contracts\ShouldHaveRoles) {
                    foreach ($this->groups()->with('roles')->get() as $group) {
                        $permissions = $permissions->concat($group->permissions);
                    }
                }

                return $permissions->unique();
            }
        )->shouldCache();
    }

    /**
     * このユーザーが指定されたパーミッションを持っているか？
     *
     * @param string $permission パーミッションの識別子(通常はクラス名)
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions->contains($permission)
            || $this->permissions->contains(\Green\AdminAuth\Permissions\Super::class);
    }
}
