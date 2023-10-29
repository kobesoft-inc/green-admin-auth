<?php

namespace Green\AdminBase\Models;

use Green\AdminBase\Traits\HasNodeOptions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Kalnoy\Nestedset\NodeTrait;

/**
 * 管理グループ
 *
 * @property string $name
 * @property \Illuminate\Database\Eloquent\Collection $users
 * @property \Illuminate\Database\Eloquent\Collection $roles
 * @property \Illuminate\Support\Collection $permissions
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class AdminGroup extends Model
{
    use NodeTrait, HasNodeOptions;

    /**
     * 一括代入できる属性
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'parent_id',
    ];

    /**
     * 起動時の処理
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        // 削除時
        static::deleting(function (AdminGroup $adminGroup) {
            // ロールの関連を削除
            $adminGroup->roles()->detach();
        });
    }

    /**
     * このグループに所属する管理ユーザー
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminUser::class,
            'admin_user_group',
            'admin_group_id',
            'admin_user_id'
        );
    }

    /**
     * このグループに割り当てられたロール
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminRole::class,
            'admin_group_role',
            'admin_group_id',
            'admin_role_id'
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
