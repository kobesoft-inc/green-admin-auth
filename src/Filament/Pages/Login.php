<?php

namespace Green\AdminAuth\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Green\AdminAuth\Models\AdminUser;
use Green\AdminAuth\Plugin;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

/**
 * ログインページ
 */
class Login extends \Filament\Pages\Auth\Login
{
    protected static string $view = 'green::filament.pages.login';

    /**
     * ログイン処理を行う
     *
     * @return mixed
     */
    public function login(): mixed
    {
        // 通常のログイン処理
        $loginResponse = parent::authenticate();
        if ($loginResponse === null) {
            return null;
        }

        // パスワードの有効期限が切れている場合の処理
        /** @var AdminUser $user */
        $user = Filament::auth()->user();
        if ($user->isPasswordExpired()) {
            // セッションに、ログインしようとしたユーザーIDを設定する
            session()->put(PasswordExpired::PASSWORD_EXPIRED_USER_ID, $user->id);

            // ログアウト処理をする
            Filament::auth()->logout();

            // パスワード有効期限切れのページにリダイレクトする
            return $this->redirectToPasswordExpired();
        }

        // ログインOK
        return $loginResponse;
    }

    /**
     * ログインの見出し
     *
     * @return string|Htmlable
     */
    public function getHeading(): string|Htmlable
    {
        return __('green::admin-auth.pages.login.heading');
    }

    /**
     * ユーザー名またはメールアドレスの入力フォームを取得する
     *
     * @return Component
     */
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label($this->getEmailFormLabel())
            ->required()
            ->email(!Plugin::get()->canLoginWithUsername())
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    /**
     * ユーザー名またはメールアドレスのラベルを取得する
     *
     * @return string
     */
    protected function getEmailFormLabel(): string
    {
        $canLoginWithEmail = Plugin::get()->canLoginWithEmail();
        $canLoginWithUsername = Plugin::get()->canLoginWithUsername();
        if ($canLoginWithEmail && $canLoginWithUsername) {
            return __('green::admin-auth.pages.login.username-or-email');
        } elseif ($canLoginWithUsername) {
            return __('green::admin-auth.pages.login.username');
        } elseif ($canLoginWithEmail) {
            return __('green::admin-auth.pages.login.email');
        } else {
            throw new \RuntimeException('Please enable login with (username or email)');
        }
    }

    /**
     * フォーム入力から認証情報を取得する
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => function (Builder $query) use ($data) {
                if (Plugin::get()->canLoginWithEmail()) {
                    $query->orWhere('email', $data['email']);
                }
                if (Plugin::get()->canLoginWithUsername()) {
                    $query->orWhere('username', $data['email']);
                }
            },
            'password' => $data['password'],
            'is_active' => true,
        ];
    }

    /**
     * パスワード有効期限切れのページにリダイレクトする
     *
     * @return mixed
     */
    public function redirectToPasswordExpired(): mixed
    {
        return redirect()->route("filament." . filament()->getCurrentPanel()->getId() . ".password-expired");
    }
}
