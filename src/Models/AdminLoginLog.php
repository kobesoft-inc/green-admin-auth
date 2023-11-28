<?php

namespace Green\AdminAuth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * 管理ユーザーのログイン履歴
 *
 * @property int $admin_user_id
 * @property string $languages
 * @property string $device
 * @property string $platform
 * @property string $browser
 * @property string $ip_address
 * @property Carbon $created_at
 */
class AdminLoginLog extends Model
{
    const UPDATED_AT = null;

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
