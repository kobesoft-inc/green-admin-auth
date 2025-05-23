<?php

namespace Green\AdminAuth\Models\Base;

use Green\AdminAuth\Models\AdminUser;
use Green\AdminAuth\Models\Concerns\GuessesRelated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * ユーザーのOAuth情報 基本クラス
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
class BaseOAuth extends Model
{
    use GuessesRelated;

    /**
     * 一括代入できる属性
     *
     * @var string[]
     */
    protected $fillable = [
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
     * テーブル名を取得する
     *
     * @return string テーブル名
     */
    public function getTable(): string
    {
        return Str::replaceLast(
            'o_auth', 'oauth',
            Str::snake(class_basename(static::class))
        );
    }

    /**
     * ユーザー
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(static::userClass(), static::userForeignKey());
    }
}
