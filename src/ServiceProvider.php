<?php

namespace Green\AdminAuth;

use Green\AdminAuth\Facades\PermissionRegistry;
use Green\AdminAuth\Filament\Pages\PasswordExpired;
use Green\AdminAuth\Listeners\LogAdminLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

/**
 * 管理画面の認証機能のサービスプロバイダー
 *
 * @package Green\AdminAuth
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * アプリケーションサービスを登録する
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(PermissionRegistry::class);
    }

    /**
     * アプリケーションサービスの起動処理を行う
     *
     * @return void
     */
    public function boot(): void
    {
        // 言語・ビュー・マイグレーションの登録
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'green');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'green');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // イベントリスナーの登録
        Event::listen(Login::class, LogAdminLogin::class);

        // 権限の登録
        PermissionRegistry::register([
            Permissions\Super::class,
            Permissions\ManageAdminUser::class,
            Permissions\ManageAdminUserInGroup::class,
            Permissions\EditAdminUserRole::class,
            Permissions\ResetAdminUserPassword::class,
            Permissions\DeleteAdminUser::class,
            Permissions\ManageAdminGroup::class,
            Permissions\ManageAdminRole::class,
        ]);

        // Livewireコンポーネントの登録
        Livewire::component('green.admin-base.filament.pages.password-expired', PasswordExpired::class);
    }
}
