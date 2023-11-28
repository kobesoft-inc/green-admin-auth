<?php

namespace Green\AdminAuth;

use Green\AdminAuth\Facades\PermissionManager;
use Green\AdminAuth\Filament\Pages\PasswordExpired;
use Green\AdminAuth\Listeners\LogAdminLogin;
use Green\AdminAuth\Permissions;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * 登録処理
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(PermissionManager::class);
    }

    /**
     * 初期起動処理
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'green');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'green');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Event::listen(Login::class, LogAdminLogin::class);

        PermissionManager::register([
            Permissions\Super::class,
            Permissions\ManageAdminUser::class,
            Permissions\ManageAdminUserInGroup::class,
            Permissions\EditAdminUserRole::class,
            Permissions\ResetAdminUserPassword::class,
            Permissions\DeleteAdminUser::class,
            Permissions\ManageAdminGroup::class,
            Permissions\ManageAdminRole::class,
        ]);

        Livewire::component('green.admin-base.filament.pages.password-expired', PasswordExpired::class);
    }
}
