<?php

namespace Green\AdminAuth\IdProviders;

use Exception;
use Filament\Actions\Action;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class MicrosoftEntraId extends IdProvider
{
    protected ?string $driver = 'azure';

    /**
     * インスタンスを生成する
     *
     * @return MicrosoftEntraId
     */
    public static function make(): self
    {
        return new static();
    }

    /**
     * テナントIDを設定する
     *
     * @param string $tenant テナントID
     * @return MicrosoftEntraId
     */
    public function tenant(string $tenant): self
    {
        $this->config['tenant'] = $tenant;
        return $this;
    }

    /**
     * ログインボタンを取得する
     *
     * @return Action
     */
    public function getLoginAction(): Action
    {
        return Action::make('login-with-microsoft-entraid')
            ->label('Microsoftアカウントでログイン')
            ->outlined()
            ->icon('bi-microsoft')
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
            ->with(['prompt' => 'select_account'])
            ->scopes(['openid', 'profile', 'email', 'offline_access', 'User.Read', ...$this->scopes])
            ->stateless()
            ->redirect();
    }

    /**
     * Graph APIのクライアントを取得する
     *
     * @return PendingRequest
     * @throws Exception
     */
    public function getGraphClient(): PendingRequest
    {
        return Http::withHeader('Authorization', 'Bearer ' . $this->user()->token);
    }

    /**
     * アバターのハッシュ値を取得する
     *
     * @return string|null
     * @throws RequestException
     */
    public function getAvatarHash(): ?string
    {
        $response = $this->getGraphClient()
            ->withHeader('content-type', 'application/json')
            ->get('https://graph.microsoft.com/v1.0/me/photo');
        if ($response->status() === 404) {
            return null;
        }
        return $response->throw()->json()['@odata.mediaEtag'];
    }

    /**
     * アバターのデータを取得する
     *
     * @return string
     * @throws Exception
     */
    public function getAvatarData(): string
    {
        return $this->getGraphClient()
            ->get('https://graph.microsoft.com/v1.0/me/photo/$value')
            ->throw();
    }
}
