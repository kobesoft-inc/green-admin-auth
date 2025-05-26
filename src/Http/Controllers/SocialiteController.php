<?php

namespace Green\AdminAuth\Http\Controllers;

use Exception;
use Filament\Facades\Filament;
use Filament\Models\Contracts\HasAvatar;
use Green\AdminAuth\Facades\IdProviderRegistry;
use Green\AdminAuth\IdProviders\IdProvider;
use Green\AdminAuth\Models\AdminOAuth;
use Green\AdminAuth\Models\AdminUser;
use Green\AdminAuth\Models\Base\BaseOAuth;
use Green\AdminAuth\Models\User\Contracts\CanBeSuspended;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
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
        $oauth = $this->getOAuth($provider);
        if ($oauth->exists && $oauth->user !== null) {
            $adminUser = $oauth->user;
        } else {
            $adminUser = $this->getUser($provider);
            abort_if(!$adminUser, 403, __('filament-panels::pages/auth/login.messages.failed'));
            $oauth->admin_user_id = $adminUser->id;
        }

        // ユーザー情報を更新する
        if ($provider->shouldUpdateUser()) {
            $adminUser = $provider->fillUser($adminUser);
            $adminUser = $this->updateAvatar($adminUser, $oauth, $provider);
        }

        // 保存
        $adminUser->save();
        $oauth->save();

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
     * @return BaseOAuth|null 認証情報
     * @throws Exception
     */
    private function getOAuth(IdProvider $provider): ?BaseOAuth
    {
        $userClass = $this->getAuthProviderModel();
        $socialUser = $provider->user();
        $oauth = ($userClass::oauthClass())::firstOrNew([
            'driver' => $provider->getDriver(),
            'uid' => $socialUser->getId(),
        ]);
        $oauth->fill([
            'token' => $socialUser->token,
            'token_expires_at' => $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null,
            'refresh_token' => $socialUser->refreshToken,
            'avatar_hash' => $provider->getAvatarHash(),
            'data' => $socialUser->user,
        ]);
        return $oauth;
    }

    /**
     * Socialiteの認証情報から管理ユーザーを取得する
     *
     * @param IdProvider $provider 認証サービス
     * @return AdminUser|null 管理ユーザー
     * @throws Exception
     */
    private function getUser(IdProvider $provider): ?AdminUser
    {
        $userClass = $this->getAuthProviderModel();
        $socialiteUser = $provider->user();
        $user = $userClass::where('email', $socialiteUser->getEmail())->first();
        if ($user && $user instanceof CanBeSuspended && $user->isSuspended()) {
            return null; // ログインできない
        }
        if ($user) {
            return $user; // 既存のユーザー
        }
        if ($provider->shouldCreateUser()) {
            // 新規ユーザーを作成する
            $user = new $userClass([
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
            ]);
            $user = $provider->fillUser($user);
            $user->save();
            return $user;
        }
        return null; // ログインできない
    }

    /**
     * アバターを更新する
     *
     * @param HasAvatar $user
     * @param BaseOAuth $oauth
     * @param IdProvider $provider
     * @return HasAvatar
     */
    private function updateAvatar(HasAvatar $user, BaseOAuth $oauth, IdProvider $provider): HasAvatar
    {
        // アバターを更新すべきか？
        if (!$oauth->isDirty('avatar_hash') || $oauth->avatar_hash === null) {
            return $user;
        }

        // アバターをダウンロードして、更新する
        $contents = $provider->getAvatarData();
        if (($extension = $this->getAvatarExtension($contents)) !== null) {
            $user->{$user->getAvatarColumn()} = 'admin-users/avatars/' . md5($contents) . '.' . $extension;
            Storage::disk('public')->put($user->{$user->getAvatarColumn()}, $contents);
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

    /**
     * 現在のGuardのユーザーモデルのインスタンスを取得する
     *
     * @return string ユーザーモデルのクラス名
     */
    protected function getAuthProviderModel(): string
    {
        $guard = Auth::guard(\filament()->getAuthGuard());
        $provider = $guard->getProvider();
        if (!$provider instanceof EloquentUserProvider) {
            throw new RuntimeException('The current provider is not an EloquentUserProvider.');
        }
        return $provider->getModel();
    }
}
