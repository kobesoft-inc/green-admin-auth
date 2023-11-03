<?php

namespace Green\AdminBase\Models;

use Green\AdminBase\Traits\HasSortOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * 管理ロール
 *
 * @property string $name
 * @property \Illuminate\Database\Eloquent\Collection $users
 * @property \Illuminate\Database\Eloquent\Collection $groups
 * @property \Illuminate\Support\Collection $permissions
 */
class AdminRole extends Model
{
    use HasSortOrder;

    /**
     * 一括代入できる属性
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'permissions',
    ];

    /**
     * キャストする属性
     *
     * @var array<string, string>
     */
    protected $casts = [
        'permissions' => 'json',
    ];

    /**
     * このロールが割り当てられた管理ユーザー
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminUser::class,
            'admin_user_role',
            'admin_role_id',
            'admin_user_id'
        );
    }

    /**
     * このロールが割り当てられたグループ
     *
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminGroup::class,
            'admin_group_role',
            'admin_role_id',
            'admin_group_id'
        );
    }

    /**
     * ロールの選択肢
     *
     * @return array
     */
    static public function getOptions(): array
    {
        return static::defaultOrder()->get()->pluck('name', 'id')->toArray();
    }
}
