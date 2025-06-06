<?php

namespace Green\AdminAuth\IdProviders;

use Exception;
use Filament\Actions\Action;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class GoogleCloudIdentity extends IdProvider
{
    protected ?string $driver = 'google';

    /**
     * インスタンスを生成する
     *
     * @return GoogleCloudIdentity
     */
    public static function make(): self
    {
        return new static();
    }

    /**
     * ログインボタンを取得する
     *
     * @return Action
     */
    public function getLoginAction(): Action
    {
        return Action::make('login-with-google-cloud-identity')
            ->label(__('green::admin-auth.pages.login.login-with-google'))
            ->outlined()
            ->url($this->redirectUrl());
    }

    /**
     * 認証ページにリダイレクトする
     *
     * @return mixed
     * @throws Exception
     */
    public function redirect(): mixed
    {
        return $this->getSocialite()
            ->with(['access_type' => 'offline', 'prompt' => 'consent select_account'])
            ->scopes(['openid', 'profile', 'email', ...$this->scopes])
            ->stateless()
            ->redirect();
    }

    /**
     * アバターのハッシュ値を取得する
     *
     * @return string|null
     * @throws Exception
     */
    public function getAvatarHash(): ?string
    {
        return md5($this->getAvatarData());
    }

    /**
     * アバターのデータを取得する
     *
     * @return string
     * @throws RequestException
     */
    public function getAvatarData(): string
    {
        return Http::get($this->user()->getAvatar())->throw();
    }
}
