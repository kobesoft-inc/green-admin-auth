<?php

namespace Green\AdminBase;

use Green\AdminBase\Facades\PermissionManager;
use Green\AdminBase\Filament\Pages\PasswordExpired;
use Green\AdminBase\Listeners\LogAdminLogin;
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
            \Green\AdminBase\Permissions\Super::class,
            \Green\AdminBase\Permissions\ManageAdminUser::class,
            \Green\AdminBase\Permissions\ManageAdminUserInGroup::class,
            \Green\AdminBase\Permissions\EditAdminUserRole::class,
            \Green\AdminBase\Permissions\ResetAdminUserPassword::class,
            \Green\AdminBase\Permissions\DeleteAdminUser::class,
            \Green\AdminBase\Permissions\ManageAdminGroup::class,
            \Green\AdminBase\Permissions\ManageAdminRole::class,
        ]);

        Livewire::component('green.admin-base.filament.pages.password-expired', PasswordExpired::class);
    }
}