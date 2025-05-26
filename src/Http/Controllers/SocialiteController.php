<?php

namespace Green\AdminAuth\Http\Controllers;

use Exception;
use Filament\Facades\Filament;
use Filament\Models\Contracts\HasAvatar;
use Green\AdminAuth\Facades\IdProviderRegistry;
use Green\AdminAuth\IdProviders\IdProvider;
use Green\AdminAuth\Models\AdminOAuth;
use Green\AdminAuth\Models\AdminUser;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SocialiteController
{
    /**
     * 認証サービスにリダイレクトする
     *
     * @param string $driver
     * @return RedirectResponse
     * @throws Exception
     */
    public function redirect(string $driver): RedirectResponse
    {
        $provider = IdProviderRegistry::get($driver, Filament::getAuthGuard());
        return $provider->redirect();
    }

    /**
     * 認証サービスからのコールバック
     *
     * @param string $driver
     * @return RedirectResponse
     * @throws Exception
     */
    public function callback(string $driver): RedirectResponse
    {
        // 認証サービスを取得する
        $provider = IdProviderRegistry::get($driver, Filament::getAuthGuard());

        // 認証情報を取得する
        $adminOAuth = $this->getAdminOAuth($provider);
        if ($adminOAuth->exists && $adminOAuth->user !== null) {
            $adminUser = $adminOAuth->user;
        } else {
            $adminUser = $this->getAdminUser($provider);
            abort_if(!$adminUser, 403, __('filament-panels::pages/auth/login.messages.failed'));
            $adminOAuth->admin_user_id = $adminUser->id;
        }

        // ユーザー情報を更新する
        if ($provider->shouldUpdateUser()) {
            $adminUser = $provider->fillUser($adminUser);
            $adminUser = $this->updateAvatar($adminUser, $adminOAuth, $provider);
        }

        // 保存
        $adminUser->save();
        $adminOAuth->save();

        // ログインする
        Filament::auth()->login($adminUser);

        // セッションを再生成する
        session()->regenerate();

        // リダイレクトする
        return redirect()->intended(Filament::getUrl());
    }

    /**
     * 既存の認証情報があればそれを返し、なければ新規作成する
     *
     * @param IdProvider $provider 認証サービス
     * @return AdminOAuth|null
     * @throws Exception
     */
    private function getAdminOAuth(IdProvider $provider): ?AdminOAuth
    {
        $socialUser = $provider->user();
        $adminOAuth = AdminOAuth::firstOrNew([
            'driver' => $provider->getDriver(),
            'uid' => $socialUser->getId(),
        ]);
        $adminOAuth->fill([
            'token' => $socialUser->token,
            'token_expires_at' => $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null,
            'refresh_token' => $socialUser->refreshToken,
            'avatar_hash' => $provider->getAvatarHash(),
            'data' => $socialUser->user,
        ]);
        return $adminOAuth;
    }

    /**
     * Socialiteの認証情報から管理ユーザーを取得する
     *
     * @param IdProvider $provider 認証サービス
     * @return AdminUser|null 管理ユーザー
     * @throws Exception
     */
    private function getAdminUser(IdProvider $provider): ?AdminUser
    {
        $socialiteUser = $provider->user();
        $adminUser = AdminUser::where('email', $socialiteUser->getEmail())->first();
        if ($adminUser && !$adminUser->is_active) {
            return null; // ログインできない
        }
        if ($adminUser) {
            return $adminUser; // 既存のユーザー
        }
        if ($provider->shouldCreateUser()) {
            // 新規ユーザーを作成する
            $adminUser = new AdminUser([
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
                'is_active' => true,
            ]);
            $adminUser = $provider->fillUser($adminUser);
            $adminUser->save();
            return $adminUser;
        }
        return null; // ログインできない
    }

    /**
     * アバターを更新する
     *
     * @param HasAvatar $user
     * @param AdminOAuth $adminOAuth
     * @param IdProvider $provider
     * @return HasAvatar
     */
    private function updateAvatar(HasAvatar $user, AdminOAuth $adminOAuth, IdProvider $provider): HasAvatar
    {
        // アバターを更新すべきか？
        if (!$adminOAuth->isDirty('avatar_hash') || $adminOAuth->avatar_hash === null) {
            return $user;
        }

        // アバターをダウンロードして、更新する
        $contents = $provider->getAvatarData();
        if (($extension = $this->getAvatarExtension($contents)) !== null) {
            $user->avatar = 'admin-users/avatars/' . md5($contents) . '.' . $extension;
            Storage::disk('public')->put($user->avatar, $contents);
        }
        return $user;
    }

    /**
     * アバターの拡張子を取得する
     *
     * @param string $contents アバターのデータ
     * @return string|null 拡張子
     */
    private function getAvatarExtension(string $contents): ?string
    {
        $finfo = finfo_open();
        $mimeType = finfo_buffer($finfo, $contents, FILEINFO_MIME_TYPE);
        finfo_close($finfo);
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => null,
        };
    }
}
