<?php

namespace Green\AdminBase\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * 管理ロール
 */
class AdminRole extends Model
{
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
            AdminGroup::class,
            'admin_group_role',
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
}
