<?php

namespace Green\AdminBase\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 管理ユーザー
 */
class AdminUser extends \Illuminate\Foundation\Auth\User
{
    use SoftDeletes;

    /**
     * 一括代入できる属性
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * 配列に非表示にする属性
     *
     * @var string[]
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * キャストする属性
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * この管理ユーザーが所属するグループ
     *
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminRole::class,
            'admin_user_group',
            'admin_user_id',
            'admin_group_id'
        );
    }

    /**
     * この管理ユーザーに割り当てられたロール
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminRole::class,
            'admin_user_role',
            'admin_user_id',
            'admin_role_id'
        );
    }
}
