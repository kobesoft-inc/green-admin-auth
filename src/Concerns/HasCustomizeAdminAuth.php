<?php

namespace Green\AdminAuth\Concerns;

use Filament\Facades\Filament;
use Green\AdminAuth\Facades\IdProviderRegistry;
use Green\AdminAuth\IdProviders\IdProvider;
use Green\AdminAuth\Models\AdminGroup;
use Green\AdminAuth\Models\AdminUser;
use Green\AdminAuth\GreenAdminAuthPlugin;
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
    private bool $passwordMustUseSymbols = false;
    private bool $passwordMustUseNumbers = false;
    private int $passwordDays = 0;
    private bool $canChangePassword = true;
    private array $userTabs = [];
    private ?string $navigationGroup = null;
    private array $idProviders = [];
    private bool $resourceDisabled = false;

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
     * @return GreenAdminAuthPlugin
     */
    public function loginWithEmail(bool $loginWithEmail): GreenAdminAuthPlugin
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
     * @return GreenAdminAuthPlugin
     */
    public function loginWithUsername(bool $loginWithUsername): GreenAdminAuthPlugin
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
     * @return GreenAdminAuthPlugin
     */
    public function disableAvatar(bool $avatarDisabled = true): GreenAdminAuthPlugin
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
     * @return GreenAdminAuthPlugin
     */
    public function disableEmail(bool $emailDisabled = true): GreenAdminAuthPlugin
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
     * @return GreenAdminAuthPlugin
     */
    public function disableUsername(bool $usernameDisabled = true): GreenAdminAuthPlugin
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
     * @return GreenAdminAuthPlugin
     */
    public function userModel(?string $userModel): GreenAdminAuthPlugin
    {
        Gate::policy(AdminUser::class, AdminUserPolicy::class);
        Gate::policy($userModel, AdminUserPolicy::class);
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
     * @return GreenAdminAuthPlugin
     */
    public function userModelLabel(?string $userModelLabel): GreenAdminAuthPlugin
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
     * @return GreenAdminAuthPlugin
     */
    public function multipleGroups(bool $multipleGroups = true): GreenAdminAuthPlugin
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
     * @return GreenAdminAuthPlugin
     */
    public function multipleRoles(bool $multipleRoles): GreenAdminAuthPlugin
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
     * @return GreenAdminAuthPlugin
     */
    public function disableGroup(bool $groupDisabled = true): GreenAdminAuthPlugin
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
     * @return GreenAdminAuthPlugin
     */
    public function groupModel(?string $groupModel): GreenAdminAuthPlugin
    {
        Gate::policy($groupModel, AdminGroupPolicy::class);
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
     * @return GreenAdminAuthPlugin
     */
    public function groupModelLabel(?string $groupModelLabel): GreenAdminAuthPlugin
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
     * @return GreenAdminAuthPlugin
     */
    public function generatedPasswordLength(int $generatedPasswordLength): GreenAdminAuthPlugin
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
     * @return GreenAdminAuthPlugin
     */
    public function passwordMinLength(int $passwordMinLength): GreenAdminAuthPlugin
    {
        $this->passwordMinLength = $passwordMinLength;
        return $this;
    }

    /**
     * パスワードに記号が必要かを取得する
     *
     * @return bool
     */
    public function getPasswordMustUseSymbols(): bool
    {
        return $this->passwordMustUseSymbols;
    }

    /**
     * パスワードに記号が必要かを設定する
     *
     * @param bool $use
     * @return GreenAdminAuthPlugin
     */
    public function passwordMustUseSymbols(bool $use = true): GreenAdminAuthPlugin
    {
        $this->passwordMustUseSymbols = $use;
        return $this;
    }

    /**
     * パスワードに数字が必要かを取得する
     *
     * @return bool
     */
    public function getPasswordMustUseNumbers(): bool
    {
        return $this->passwordMustUseNumbers;
    }

    /**
     * パスワードに数字が必要かを設定する
     *
     * @param bool $use
     * @return GreenAdminAuthPlugin
     */
    public function passwordMustUseNumbers(bool $use = true): GreenAdminAuthPlugin
    {
        $this->passwordMustUseNumbers = $use;
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
     * @return GreenAdminAuthPlugin
     */
    public function passwordDays(int $passwordDays): GreenAdminAuthPlugin
    {
        $this->passwordDays = $passwordDays;
        return $this;
    }

    /**
     * パスワードを変更できるないようにする
     */
    public function disableChangePassword(bool $canChangePassword = true): GreenAdminAuthPlugin
    {
        $this->canChangePassword = !$canChangePassword;
        return $this;
    }

    /**
     * パスワードを変更できるか取得する
     */
    public function canChangePassword(): bool
    {
        return $this->canChangePassword;
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
     * @return GreenAdminAuthPlugin
     */
    public function userTabs(array $userTabs): GreenAdminAuthPlugin
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
     * @return GreenAdminAuthPlugin
     */
    public function navigationGroup(?string $navigationGroup): GreenAdminAuthPlugin
    {
        $this->navigationGroup = $navigationGroup;
        return $this;
    }

    /**
     * ページが無効化されているかを取得する
     *
     * @return bool
     */
    public function isResourceDisabled(): bool
    {
        return $this->resourceDisabled;
    }

    /**
     * ページを無効化する
     *
     * @param bool $isResourceDisabled
     * @return GreenAdminAuthPlugin
     */
    public function disableResource(bool $isResourceDisabled = true): GreenAdminAuthPlugin
    {
        $this->resourceDisabled = $isResourceDisabled;
        return $this;
    }

    /**
     * IdPを全て取得する
     *
     * @return array
     */
    public function getIdProviders(): array
    {
        return IdProviderRegistry::all();
    }

    /**
     * 特定のIdPを取得する
     *
     * @param string $driver
     * @return IdProvider
     */
    public function getIdProvider(string $driver): IdProvider
    {
        return IdProviderRegistry::get($driver);
    }

    /**
     * IdPを追加する
     *
     * @param IdProvider $idProvider
     * @return GreenAdminAuthPlugin
     */
    public function idProvider(IdProvider $idProvider): GreenAdminAuthPlugin
    {
        IdProviderRegistry::register($idProvider);
        return $this;
    }
}
