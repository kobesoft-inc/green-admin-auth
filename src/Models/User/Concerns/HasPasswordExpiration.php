<?php

namespace Green\AdminAuth\Models\User\Concerns;

use Green\AdminAuth\Models\User\Contracts\ShouldExpirePassword;
use Illuminate\Database\Eloquent\Model;

/**
 * ユーザーはパスワードの有効期限を持つ
 *
 * @mixin Model|ShouldExpirePassword
 */
trait HasPasswordExpiration
{
    /**
     * 起動時の処理
     */
    public function HasPasswordExpirationBooted(): void
    {
        static::saving(function (Model|ShouldExpirePassword $model) {
            if ($model->isDirty('password') && !$model->isDirty('password_expires_at')) {
                // パスワードを変更し、有効期限を設定していない場合には、自動的に有効期限を設定する
                $passwordDays = $model->defaultPasswordValidityInDays();
                $model->{$model->passwordExpiresAtColumn()} = $passwordDays
                    ? now()->addDays($passwordDays) // 有効期限を設定
                    : null; // 有効期限無し
            }
        });
    }

    /**
     * パスワードの有効期限のカラム名を取得する
     *
     * @return string パスワードの有効期限のカラム名
     */
    public function passwordExpiresAtColumn(): string
    {
        return 'password_expires_at';
    }

    /**
     * パスワードのデフォルトの有効日数を取得する
     *
     * @return int|null パスワードのデフォルトの有効日数
     */
    public function defaultPasswordValidityInDays(): ?int
    {
        return null;
    }

    /**
     * パスワードの有効期限が切れたか？
     *
     * @return bool パスワードの有効期限が切れたらtrue
     */
    public function isPasswordExpired(): bool
    {
        return $this->{$this->passwordExpiresAtColumn()}?->isPast() ?? false;
    }
}
