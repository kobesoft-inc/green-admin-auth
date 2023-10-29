<?php

namespace Green\AdminBase;

use Filament\Panel;
use Green\AdminBase\Filament\Pages\Login;
use Green\AdminBase\Filament\Pages\PasswordExpired;
use Green\AdminBase\Filament\Resources\AdminGroupResource;
use Green\AdminBase\Filament\Resources\AdminRoleResource;
use Green\AdminBase\Filament\Resources\AdminUserResource;
use Illuminate\Support\Facades\Route;

class Plugin implements \Filament\Contracts\Plugin
{
    private bool $loginWithEmail = true;
    private bool $loginWithUsername = true;
    private bool $multipleGroups = true;
    private bool $multipleRoles = true;
    private int $generatedPasswordLength = 12;
    private int $passwordMinLength = 8;
    private int $passwordDays = 0;

    /**
     * プラグインの識別子を返す
     *
     * @return string
     */
    public function getId(): string
    {
        return 'adminBase';
    }

    /**
     * 登録処理
     *
     * @param  Panel  $panel
     * @return void
     */
    public function register(Panel $panel): void
    {
        $routes = $panel->getRoutes();
        $panel
            ->resources([
                AdminUserResource::class,
                AdminGroupResource::class,
                AdminRoleResource::class,
            ])
            ->pages([
            ])
            ->routes(function ($panel) use ($routes) {
                if ($routes) {
                    $routes($panel);
                }
                Route::get('/password-expired', PasswordExpired::class)->name('password-expired');
            })
            ->login(Login::class);
    }

    /**
     * 初期起動処理
     *
     * @param  Panel  $panel
     * @return void
     */
    public function boot(Panel $panel): void
    {
    }

    /**
     * インスタンスを生成する
     *
     * @return static
     */
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * インスタンスを取得する
     *
     * @return static
     */
    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }

    /**
     * ユーザーがメールアドレスでログインできるかを取得する
     *
     * @return bool
     */
    public function canLoginWithEmail(): bool
    {
        return $this->loginWithEmail;
    }

    /**
     * ユーザーがメールアドレスでログインできるかを設定する
     *
     * @param  bool  $loginWithEmail
     * @return Plugin
     */
    public function loginWithEmail(bool $loginWithEmail): Plugin
    {
        $this->loginWithEmail = $loginWithEmail;
        return $this;
    }

    /**
     * ユーザーがユーザー名でログインできるかを取得する
     *
     * @return bool
     */
    public function canLoginWithUsername(): bool
    {
        return $this->loginWithUsername;
    }

    /**
     * ユーザーがユーザー名でログインできるかを設定する
     *
     * @param  bool  $loginWithUsername
     * @return Plugin
     */
    public function loginWithUsername(bool $loginWithUsername): Plugin
    {
        $this->loginWithUsername = $loginWithUsername;
        return $this;
    }

    /**
     * ユーザーが複数のグループに所属できるかを取得する
     *
     * @return bool
     */
    public function isMultipleGroups(): bool
    {
        return $this->multipleGroups;
    }

    /**
     * ユーザーが複数のグループに所属できるかを設定する
     *
     * @param  bool  $multipleGroups
     * @return Plugin
     */
    public function multipleGroups(bool $multipleGroups): Plugin
    {
        $this->multipleGroups = $multipleGroups;
        return $this;
    }

    /**
     * ユーザーが複数のロールに所属できるかを取得する
     *
     * @return bool
     */
    public function isMultipleRoles(): bool
    {
        return $this->multipleRoles;
    }

    /**
     * ユーザーが複数のロールに所属できるかを設定する
     *
     * @param  bool  $multipleRoles
     * @return Plugin
     */
    public function multipleRoles(bool $multipleRoles): Plugin
    {
        $this->multipleRoles = $multipleRoles;
        return $this;
    }

    /**
     * 生成パスワードのルールを取得する
     *
     * @return int
     */
    public function getGeneratedPasswordLength(): int
    {
        return $this->generatedPasswordLength;
    }

    /**
     * 生成パスワードのルールを設定する
     *
     * @param  int  $generatedPasswordLength
     * @return Plugin
     */
    public function generatedPasswordLength(int $generatedPasswordLength): Plugin
    {
        $this->generatedPasswordLength = $generatedPasswordLength;
        return $this;
    }

    /**
     * パスワードの最小の長さを取得する
     *
     * @return int
     */
    public function getPasswordMinLength(): int
    {
        return $this->passwordMinLength;
    }

    /**
     * パスワードの最小の長さを設定する
     *
     * @param  int  $passwordMinLength
     * @return Plugin
     */
    public function passwordMinLength(int $passwordMinLength): Plugin
    {
        $this->passwordMinLength = $passwordMinLength;
        return $this;
    }

    /**
     * パスワードの有効日数を取得する
     *
     * @return int
     */
    public function getPasswordDays(): int
    {
        return $this->passwordDays;
    }

    /**
     * パスワードの有効日数を設定する
     *
     * @param  int  $passwordDays
     * @return Plugin
     */
    public function passwordDays(int $passwordDays): Plugin
    {
        $this->passwordDays = $passwordDays;
        return $this;
    }


}