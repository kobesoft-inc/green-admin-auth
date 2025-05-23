<?php

namespace Green\AdminAuth\Models\Base;

use Green\AdminAuth\Models\Concerns\GuessesRelated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * ユーザーのログイン履歴 基本クラス
 *
 * @property string $languages
 * @property string $device
 * @property string $platform
 * @property string $browser
 * @property string $ip_address
 * @property Carbon $created_at
 */
abstract class BaseLoginLog extends Model
{
    use GuessesRelated;

    const UPDATED_AT = null;

    /**
     * 一括代入できる属性
     *
     * @var string[]
     */
    protected $fillable = [
        'languages',
        'device',
        'platform',
        'browser',
        'ip_address',
    ];

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
