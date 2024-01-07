<?php

namespace Green\AdminAuth\IdProviders;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Green\AdminAuth\Models\AdminUser;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Manager\Config;
use SocialiteProviders\Manager\OAuth2\User;

abstract class IdProvider
{
    protected ?string $driver = null;
    private string $clientId = '';
    private string $clientSecret = '';
    protected array $config = [];
    protected ?Provider $socialite = null;
    protected array $scopes = [];
    protected bool $createUser = false;
    protected bool $updateUser = true;
    protected ?Closure $userMapper = null;

    /**
     * インスタンスを生成する
     *
     * @return IdProvider インスタンス
     */
    abstract public static function make(): IdProvider;

    /**
     * ログインボタンを取得する
     *
     * @return Action
     */
    abstract public function getLoginAction(): Action;

    /**
     * 認証ページにリダイレクトする
     *
     * @return mixed
     */
    abstract public function redirect(): mixed;

    /**
     * アバターのハッシュ値を取得する
     *
     * @return string|null
     */
    abstract public function getAvatarHash(): ?string;

    /**
     * アバターのデータを取得する
     *
     * @return string
     */
    abstract public function getAvatarData(): string;

    /**
     * Socialiteのドライバー名を取得する
     *
     * @return string Socialiteのドライバー名
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * クライアントIDを設定する
     *
     * @param string $clientId クライアントID
     * @return MicrosoftEntraId
     */
    public function clientId(string $clientId): self
    {
        $this->clientId = $clientId;
        return $this;
    }


    /**
     * クライアント秘密鍵を設定する
     *
     * @param string $clientSecret クライアント秘密鍵
     * @return MicrosoftEntraId
     */
    public function clientSecret(string $clientSecret): self
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    /**
     * 追加で要求するアクセススコープ
     *
     * @param array $scopes
     * @return $this
     */
    public function scopes(array $scopes): self
    {
        $this->scopes = $scopes;
        return $this;
    }

    /**
     * リダイレクトURLを取得する
     *
     * @return string リダイレクトURL
     */
    public function redirectUrl(): string
    {
        $routeName = 'filament.' . Filament::getCurrentPanel()->getId() . '.auth.sso-redirect';
        return route($routeName, ['driver' => $this->getDriver()]);
    }

    /**
     * コールバックURLを取得する
     *
     * @return string コールバックURL
     */
    public function callbackUrl(): string
    {
        $routeName = 'filament.' . Filament::getCurrentPanel()->getId() . '.auth.sso-callback';
        return route($routeName, ['driver' => $this->getDriver()]);
    }

    /**
     * Socialiteの設定を取得する
     *
     * @return Config
     * @throws Exception
     */
    protected function getConfig(): Config
    {
        if ($this->clientId === '' || $this->clientSecret === '') {
            throw new Exception('クライアントIDとクライアント秘密鍵を設定してください');
        }
        return new Config(
            $this->clientId,
            $this->clientSecret,
            $this->callbackUrl(),
            $this->config
        );
    }

    /**
     * Socialiteの認証プロバイダーを取得する
     *
     * @return Provider
     * @throws Exception
     */
    public function getSocialite(): Provider
    {
        if ($this->socialite === null) {
            $configKey = 'services.' . $this->getDriver();
            if (config($configKey, null) === null) {
                config([$configKey => $this->getConfig()->get()]);
                $this->socialite = Socialite::driver($this->getDriver());
            } else {
                $this->socialite = Socialite::driver($this->getDriver())->setConfig($this->getConfig());
            }
        }
        return $this->socialite;
    }

    /**
     * Socialiteの認証ユーザーを取得する
     * @throws Exception
     */
    public function user(): \Laravel\Socialite\Contracts\User
    {
        return $this->getSocialite()->stateless()->user();
    }

    /**
     * ログイン時にユーザーを自動作成するか取得する
     *
     * @return bool
     */
    public function shouldCreateUser(): bool
    {
        return $this->createUser;
    }

    /**
     * ログイン時にユーザーを自動作成するか設定する
     *
     * @param bool $createUser ログイン時にユーザーを自動作成するか
     * @return $this
     */
    public function createUser(bool $createUser = true): self
    {
        $this->createUser = $createUser;
        return $this;
    }

    /**
     * ログイン時にユーザーを自動更新するか取得する
     *
     * @return bool
     */
    public function shouldUpdateUser(): bool
    {
        return $this->updateUser;
    }

    /**
     * ログイン時にユーザーを自動更新するか設定する
     *
     * @param bool $updateUser ログイン時にユーザーを自動更新するか
     * @return $this
     */
    public function updateUser(bool $updateUser = true): self
    {
        $this->updateUser = $updateUser;
        return $this;
    }

    /**
     * ユーザーデータのマッピング処理を実行する
     *
     * @param AdminUser $adminUser
     * @return AdminUser
     * @throws Exception
     */
    public function fillUser(AdminUser $adminUser): AdminUser
    {
        $socialiteUser = $this->user();
        $adminUser->fill([
            'name' => $socialiteUser->getName(),
            'email' => $socialiteUser->getEmail(),
        ]);
        return ($fn = $this->userMapper)
            ? $fn($adminUser, $socialiteUser)
            : $adminUser;
    }

    /**
     * ユーザーデータのマッピング処理を設定する
     *
     * @param Closure|null $userMapper
     * @return void
     */
    public function userMapper(?Closure $userMapper): void
    {
        $this->userMapper = $userMapper;
    }
}
