<?php

namespace Green\AdminBase\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Kalnoy\Nestedset\NodeTrait;

/**
 * 管理グループ
 */
class AdminGroup extends Model
{
    use NodeTrait;

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
}
