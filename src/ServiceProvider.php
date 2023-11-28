<?php

namespace Green\AdminAuth;

use Green\AdminAuth\Facades\PermissionManager;
use Green\AdminAuth\Filament\Pages\PasswordExpired;
use Green\AdminAuth\Listeners\LogAdminLogin;
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
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'green');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'green');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Event::listen(Login::class, LogAdminLogin::class);

        PermissionManager::register([
            \Green\AdminAuth\Permissions\Super::class,
            \Green\AdminAuth\Permissions\ManageAdminUser::class,
            \Green\AdminAuth\Permissions\ManageAdminUserInGroup::class,
            \Green\AdminAuth\Permissions\EditAdminUserRole::class,
            \Green\AdminAuth\Permissions\ResetAdminUserPassword::class,
            \Green\AdminAuth\Permissions\DeleteAdminUser::class,
            \Green\AdminAuth\Permissions\ManageAdminGroup::class,
            \Green\AdminAuth\Permissions\ManageAdminRole::class,
        ]);

        Livewire::component('green.admin-base.filament.pages.password-expired', PasswordExpired::class);
    }
}