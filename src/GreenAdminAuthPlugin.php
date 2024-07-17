<?php

namespace Green\AdminAuth;

use Filament\Navigation\MenuItem;
use Filament\Panel;
use Green\AdminAuth\AvatarProviders\MysteryManAvatarProvider;
use Green\AdminAuth\Filament\Pages\ChangePassword;
use Green\AdminAuth\Filament\Pages\Login;
use Green\AdminAuth\Filament\Pages\PasswordExpired;
use Green\AdminAuth\Filament\Resources\AdminGroupResource;
use Green\AdminAuth\Filament\Resources\AdminRoleResource;
use Green\AdminAuth\Filament\Resources\AdminUserResource;
use Green\AdminAuth\Http\Controllers\SocialiteController;
use Green\AdminAuth\Concerns\HasCustomizeAdminAuth;
use Illuminate\Support\Facades\Route;

/**
 * 管理画面の認証機能のプラグイン
 *
 * @package Green\AdminAuth
 */
class GreenAdminAuthPlugin implements \Filament\Contracts\Plugin
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
        if ($this->isAvatarDisabled()) {
            $panel->defaultAvatarProvider(MysteryManAvatarProvider::class);
        }
        $panel
            ->resources($this->isResourceDisabled() ? [] : [
                AdminUserResource::class,
                AdminGroupResource::class,
                AdminRoleResource::class,
            ])
            ->pages([
                ChangePassword::class,
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
            ->userMenuItems([
                MenuItem::make()
                    ->label(fn() => __('green::admin-auth.pages.change-password.heading'))
                    ->icon('heroicon-o-lock-closed')
                    ->url(fn() => $panel->route('pages.change-password'))
                    ->visible(fn() => GreenAdminAuthPlugin::get()->canChangePassword()),
            ])
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
