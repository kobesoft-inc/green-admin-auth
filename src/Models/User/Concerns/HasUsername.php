<?php

namespace Green\AdminAuth\Models\User\Concerns;

/**
 * ユーザー名がある
 */
trait HasUsername
{
    /**
     * ユーザー名のカラム名を取得する
     *
     * @return string ユーザー名のカラム名
     */
    public static function getUsernameColumn(): string
    {
        return 'username';
    }

    /**
     * メールアドレスでログインできるかを取得する
     *
     * @return bool メールアドレスでログインできるか？
     */
    public static function canLoginWithEmail(): bool
    {
        return true;
    }

    /**
     * ユーザー名でログインできるかを取得する
     *
     * @return bool ユーザー名でログインできるか？
     */
    public static function canLoginWithUsername(): bool
    {
        return true;
    }
}
