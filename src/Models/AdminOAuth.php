<?php

namespace Green\AdminAuth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * 管理ユーザーのログイン履歴
 *
 * @property int $id
 * @property int $admin_user_id
 * @property string $driver
 * @property string $token
 * @property Carbon $token_expires_at
 * @property string $refresh_token
 * @property string $uid
 * @property string $avatar_hash
 * @property array $data
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property AdminUser $user
 */
class AdminOAuth extends Model
{
    /**
     * 一括代入できる属性
     *
     * @var string[]
     */
    protected $fillable = [
        'admin_user_id',
        'driver',
        'token',
        'token_expires_at',
        'refresh_token',
        'uid',
        'avatar_hash',
        'data',
    ];

    /**
     * キャストする属性
     *
     * @var string[]
     */
    protected $casts = [
        'token_expires_at' => 'datetime',
        'data' => 'array',
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
