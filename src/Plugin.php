<?php

namespace Green\AdminAuth;

use Filament\Panel;
use Green\AdminAuth\Filament\Pages\Login;
use Green\AdminAuth\Filament\Pages\PasswordExpired;
use Green\AdminAuth\Filament\Resources\AdminGroupResource;
use Green\AdminAuth\Filament\Resources\AdminRoleResource;
use Green\AdminAuth\Filament\Resources\AdminUserResource;
use Green\AdminAuth\Http\Controllers\SocialiteController;
use Green\AdminAuth\Traits\HasCustomizeAdminAuth;
use Illuminate\Support\Facades\Route;

/**
 * 管理画面の認証機能のプラグイン
 *
 * @package Green\AdminAuth
 */
class Plugin implements \Filament\Contracts\Plugin
{
    use HasCustomizeAdminAuth;

    /**
     * プラグインの識別子を返す
     *
     * @return string
     */
    public function getId(): string
    {
        return 'admin-auth';
    }

    /**
     * 登録処理
     *
     * @param Panel $panel
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
                Route::get('/password-expired', PasswordExpired::class)
                    ->name('password-expired');
                Route::get('/login/{driver}', [SocialiteController::class, 'redirect'])
                    ->name('auth.sso-redirect');
                Route::get('/login/{driver}/callback', [SocialiteController::class, 'callback'])
                    ->name('auth.sso-callback');
            })
            ->login(Login::class);
    }

    /**
     * 初期起動処理
     *
     * @param Panel $panel
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
}
