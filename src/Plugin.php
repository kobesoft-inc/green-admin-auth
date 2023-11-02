<?php

namespace Green\AdminBase;

use Filament\Panel;
use Green\AdminBase\Filament\Pages\Login;
use Green\AdminBase\Filament\Pages\PasswordExpired;
use Green\AdminBase\Filament\Resources\AdminGroupResource;
use Green\AdminBase\Filament\Resources\AdminRoleResource;
use Green\AdminBase\Filament\Resources\AdminUserResource;
use Green\AdminBase\Models\AdminGroup;
use Green\AdminBase\Models\AdminUser;
use Illuminate\Support\Facades\Route;

class Plugin implements \Filament\Contracts\Plugin
{
    private bool $loginWithEmail = true;
    private bool $loginWithUsername = true;
    private bool $emailDisabled = false;
    private bool $usernameDisabled = false;
    private ?string $userModel = AdminUser::class;
    private ?string $userModelLabel = null;
    private bool $multipleGroups = false;
    private bool $multipleRoles = false;
    private bool $groupDisabled = false;
    private ?string $groupModel = AdminGroup::class;
    private ?string $groupModelLabel = null;
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
     * 管理ユーザーのメールアドレス項目が無効化されているか取得する
     *
     * @return bool
     */
    public function isEmailDisabled(): bool
    {
        return $this->emailDisabled;
    }

    /**
     * 管理ユーザーのメールアドレス項目を無効化する
     *
     * @param  bool  $emailDisabled
     * @return Plugin
     */
    public function emailDisabled(bool $emailDisabled = true): Plugin
    {
        $this->emailDisabled = $emailDisabled;
        return $this;
    }

    /**
     * 管理ユーザーのユーザー名が無効化されているか取得する
     *
     * @return bool
     */
    public function isUsernameDisabled(): bool
    {
        return $this->usernameDisabled;
    }

    /**
     * 管理ユーザーのユーザー名を無効化する
     *
     * @param  bool  $usernameDisabled
     * @return Plugin
     */
    public function usernameDisabled(bool $usernameDisabled = true): Plugin
    {
        $this->usernameDisabled = $usernameDisabled;
        return $this;
    }

    /**
     * 管理ユーザーのモデルを取得する
     *
     * @return string|null
     */
    public function getUserModel(): ?string
    {
        return $this->userModel;
    }

    /**
     * 管理ユーザーのモデルを設定する
     *
     * @param  string|null  $userModel
     * @return Plugin
     */
    public function userModel(?string $userModel): Plugin
    {
        $this->userModel = $userModel;
        return $this;
    }

    /**
     * 管理ユーザーの呼び方を取得する
     *
     * @return string|null
     */
    public function getUserModelLabel(): ?string
    {
        return $this->userModelLabel ?? __('green::admin_base.admin_user.model');
    }

    /**
     * 管理ユーザーの呼び方を設定する
     *
     * @param  string|null  $userModelLabel
     * @return Plugin
     */
    public function userModelLabel(?string $userModelLabel): Plugin
    {
        $this->userModelLabel = $userModelLabel;
        return $this;
    }

    /**
     * ユーザーが複数のグループに所属するかを取得する
     *
     * @return bool
     */
    public function isMultipleGroups(): bool
    {
        return $this->multipleGroups;
    }

    /**
     * ユーザーが複数のグループに所属するかを設定する
     *
     * @param  bool  $multipleGroups
     * @return Plugin
     */
    public function multipleGroups(bool $multipleGroups = true): Plugin
    {
        $this->multipleGroups = $multipleGroups;
        return $this;
    }

    /**
     * ユーザーが複数のロールに所属するかを取得する
     *
     * @return bool
     */
    public function isMultipleRoles(): bool
    {
        return $this->multipleRoles;
    }

    /**
     * ユーザーが複数のロールに所属するかを設定する
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
     * グループ機能が無効化されているかを取得する
     *
     * @return bool
     */
    public function isGroupDisabled(): bool
    {
        return $this->groupDisabled;
    }

    /**
     * グループ機能を無効化する
     *
     * @param  bool  $groupDisabled
     * @return Plugin
     */
    public function groupDisabled(bool $groupDisabled = true): Plugin
    {
        $this->groupDisabled = $groupDisabled;
        return $this;
    }

    /**
     * 管理グループのモデルを取得する
     *
     * @return string|null
     */
    public function getGroupModel(): ?string
    {
        return $this->groupModel;
    }

    /**
     * 管理グループのモデルを設定する
     *
     * @param  string|null  $groupModel
     * @return Plugin
     */
    public function groupModel(?string $groupModel): Plugin
    {
        $this->groupModel = $groupModel;
        return $this;
    }

    /**
     * 管理グループの呼び方を取得する
     *
     * @return string|null
     */
    public function getGroupModelLabel(): ?string
    {
        return $this->groupModelLabel ?? __('green::admin_base.admin_group.model');
    }

    /**
     * 管理グループの呼び方を設定する
     *
     * @param  string|null  $groupModelLabel
     * @return Plugin
     */
    public function groupModelLabel(?string $groupModelLabel): Plugin
    {
        $this->groupModelLabel = $groupModelLabel;
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