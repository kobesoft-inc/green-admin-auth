<?php

namespace Green\AdminBase\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 管理ユーザーのログイン履歴
 */
class AdminLoginLog extends Model
{
    /**
     * 一括代入できる属性
     *
     * @var string[]
     */
    protected $fillable = [
        'admin_user_id',
        'languages',
        'device',
        'platform',
        'browser',
        'ip_address',
    ];

    /**
     * ログイン履歴の管理ユーザー
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id');
    }
}
