<?php

namespace Green\AdminAuth\Http\Controllers;

use Exception;
use Filament\Facades\Filament;
use Green\AdminAuth\IdProviders\IdProvider;
use Green\AdminAuth\Models\AdminAuth;
use Green\AdminAuth\Models\AdminUser;
use Green\AdminAuth\Plugin;
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
        $provider = Plugin::get()->getIdProvider($driver);
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
        $provider = Plugin::get()->getIdProvider($driver);

        // 認証情報を取得する
        $adminAuth = $this->getAdminAuth($provider);
        if ($adminAuth->exists) {
            $adminUser = $adminAuth->user;
        } else {
            $adminUser = $this->getAdminUser($provider);
            abort_if(!$adminUser, 403, __('filament-panels::pages/auth/login.messages.failed'));
            $adminAuth->admin_user_id = $adminUser->id;
        }

        // ユーザー情報を更新する
        if ($provider->shouldUpdateUser()) {
            $this->updateAdminUser($adminUser, $provider);
            $this->updateAvatar($adminUser, $adminAuth, $provider);
        }

        // 認証情報を保存する
        $adminAuth->save();

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
     * @return AdminAuth|null
     * @throws Exception
     */
    private function getAdminAuth(IdProvider $provider): ?AdminAuth
    {
        $socialUser = $provider->user();
        $adminAuth = AdminAuth::firstOrNew([
            'driver' => $provider->getDriver(),
            'uid' => $socialUser->getId(),
        ]);
        $adminAuth->fill([
            'token' => $socialUser->token,
            'token_expires_at' => $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null,
            'refresh_token' => $socialUser->refreshToken,
            'uid' => $socialUser->getId(),
            'avatar_hash' => $provider->getAvatarHash(),
            'data' => $socialUser->user,
        ]);
        return $adminAuth;
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
            return AdminUser::create(array_merge(
                $provider->mapUserData($provider->user()->user),
                ['is_active' => true]
            ));
        }
        return null; // ログインできない
    }

    /**
     * 管理ユーザーを更新する
     *
     * @param AdminUser $adminUser
     * @param IdProvider $provider
     * @return void
     * @throws Exception
     */
    private function updateAdminUser(AdminUser $adminUser, IdProvider $provider): void
    {
        $socialiteUser = $provider->user();
        $adminUser->fill($provider->mapUserData($provider->user()->user));
        $adminUser->save();
    }

    /**
     * アバターを更新する
     *
     * @param AdminUser $adminUser
     * @param AdminAuth $adminAuth
     * @param IdProvider $provider
     * @return void
     */
    private function updateAvatar(AdminUser $adminUser, AdminAuth $adminAuth, IdProvider $provider): void
    {
        // アバターを更新すべきか？
        if (!$adminAuth->isDirty('avatar_hash') || $adminAuth->avatar_hash === null) {
            return;
        }

        // アバターをダウンロードして、更新する
        $contents = $provider->getAvatarData();
        $adminUser->avatar = 'admin-users/avatars/' . md5($contents);
        Storage::disk('public')->put($adminUser->avatar, $contents);
        $adminUser->save();
    }
}
