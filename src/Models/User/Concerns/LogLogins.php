<?php

namespace Green\AdminAuth\Models\User\Concerns;

use Green\AdminAuth\Models\Concerns\GuessesRelated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ログイン履歴を持つ
 *
 * @mixin Model
 */
trait LogLogins
{
    use GuessesRelated;

    /**
     * ログイン履歴を取得
     *
     * @return HasMany ログイン履歴
     */
    public function loginLogs(): HasMany
    {
        return $this->hasMany(static::loginLogClass(), static::userForeignKey());
    }
}
