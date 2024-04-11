<?php

namespace Green\AdminAuth\Concerns;

use Green\AdminAuth\IdProviders\IdProvider;
use Green\AdminAuth\Models\AdminGroup;
use Green\AdminAuth\Models\AdminUser;
use Green\AdminAuth\Plugin;
use Green\AdminAuth\Policies\AdminGroupPolicy;
use Green\AdminAuth\Policies\AdminUserPolicy;
use Illuminate\Support\Facades\Gate;

/**
 * プラグインのカスタマイズ機能
 */
trait HasCustomizeAdminAuth
{
    private bool $loginWithEmail = true;
    private bool $loginWithUsername = true;
    private bool $emailDisabled = false;
    private bool $usernameDisabled = false;
    private bool $avatarDisabled = false;
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
    private array $userTabs = [];
    private ?string $navigationGroup = null;
    private array $idProviders = [];

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
     * @param bool $loginWithEmail
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
     * @param bool $loginWithUsername
     * @return Plugin
     */
    public function loginWithUsername(bool $loginWithUsername): Plugin
    {
        $this->loginWithUsername = $loginWithUsername;
        return $this;
    }

    /**
     * 管理ユーザーのアバター項目が無効化されているか取得する
     *
     * @return bool
     */
    public function isAvatarDisabled(): bool
    {
        return $this->avatarDisabled;
    }

    /**
     * 管理ユーザーのアバター項目を無効化する
     *
     * @param bool $avatarDisabled
     * @return Plugin
     */
    public function disableAvatar(bool $avatarDisabled = true): Plugin
    {
        $this->avatarDisabled = $avatarDisabled;
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
     * @param bool $emailDisabled
     * @return Plugin
     */
    public function disableEmail(bool $emailDisabled = true): Plugin
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
     * @param bool $usernameDisabled
     * @return Plugin
     */
    public function disableUsername(bool $usernameDisabled = true): Plugin
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
     * @param string|null $userModel
     * @return Plugin
     */
    public function userModel(?string $userModel): Plugin
    {
        Gate::policy($userModel, AdminUserPolicy::class); // ポリシーの登録
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
        return $this->userModelLabel ?? __('green::admin-auth.admin-user.model');
    }

    /**
     * 管理ユーザーの呼び方を設定する
     *
     * @param string|null $userModelLabel
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
     * @param bool $multipleGroups
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
     * @param bool $multipleRoles
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
     * @param bool $groupDisabled
     * @return Plugin
     */
    public function disableGroup(bool $groupDisabled = true): Plugin
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
     * @param string|null $groupModel
     * @return Plugin
     */
    public function groupModel(?string $groupModel): Plugin
    {
        Gate::policy($groupModel, AdminGroupPolicy::class); // ポリシーの登録
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
        return $this->groupModelLabel ?? __('green::admin-auth.admin-group.model');
    }

    /**
     * 管理グループの呼び方を設定する
     *
     * @param string|null $groupModelLabel
     * @return Plugin
     */
    public function groupModelLabel(?string $groupModelLabel): Plugin
    {
        $this->groupModelLabel = $groupModelLabel;
        return $this;
    }

    /**
     * 翻訳用の単語を取得する
     *
     * @return array
     */
    public function getTranslationWords(): array
    {
        return [
            'user' => $this->getUserModelLabel(),
            'group' => $this->getGroupModelLabel(),
        ];
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
     * @param int $generatedPasswordLength
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
     * @param int $passwordMinLength
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
     * @param int $passwordDays
     * @return Plugin
     */
    public function passwordDays(int $passwordDays): Plugin
    {
        $this->passwordDays = $passwordDays;
        return $this;
    }

    /**
     * ユーザー管理ページのタブを取得する
     *
     * @return array
     */
    public function getUserTabs(): array
    {
        return $this->userTabs;
    }

    /**
     * ユーザー管理ページのタブを設定する
     *
     * @param array $userTabs
     * @return Plugin
     */
    public function userTabs(array $userTabs): Plugin
    {
        $this->userTabs = $userTabs;
        return $this;
    }

    /**
     * ナビゲーショングループを取得する
     *
     * @return string|null
     */
    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup ?? __('green::admin-auth.navigation-group');
    }

    /**
     * ナビゲーショングループを設定する
     *
     * @param string|null $navigationGroup
     * @return Plugin
     */
    public function navigationGroup(?string $navigationGroup): Plugin
    {
        $this->navigationGroup = $navigationGroup;
        return $this;
    }

    /**
     * IdPを全て取得する
     *
     * @return array
     */
    public function getIdProviders(): array
    {
        return $this->idProviders;
    }

    /**
     * 特定のIdPを取得する
     *
     * @param string $driver
     * @return IdProvider
     */
    public function getIdProvider(string $driver): IdProvider
    {
        return collect($this->idProviders)->first(fn($idProvider) => $idProvider->getDriver() === $driver);
    }

    /**
     * IdPを追加する
     *
     * @param IdProvider $idProvider
     * @return Plugin
     */
    public function idProvider(IdProvider $idProvider): Plugin
    {
        $this->idProviders[] = $idProvider;
        return $this;
    }
}
