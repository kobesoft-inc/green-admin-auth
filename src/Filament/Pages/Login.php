<?php

namespace Green\AdminBase\Filament\Pages;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class Login extends \Filament\Pages\Auth\Login
{
    /**
     * ログインの見出し
     *
     * @return string|Htmlable
     */
    public function getHeading(): string|Htmlable
    {
        return __('green::admin_base.pages.login.heading');
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
            ->email(!config('green.admin_base.users_can_login_with_username'))
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
        $email = config('green.admin_base.users_can_login_with_email');
        $username = config('green.admin_base.users_can_login_with_username');
        if ($email && $username) {
            return __('green::admin_base.pages.login.username_or_email');
        } elseif ($username) {
            return __('green::admin_base.pages.login.username');
        } elseif ($email) {
            return __('green::admin_base.pages.login.email');
        } else {
            throw new \RuntimeException('Please enable users_can_login_with_(username or email)');
        }
    }

    /**
     * フォーム入力から認証情報を取得する
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => function (Builder $query) use ($data) {
                if (config('green.admin_base.users_can_login_with_email')) {
                    $query->orWhere('email', $data['email']);
                }
                if (config('green.admin_base.users_can_login_with_username')) {
                    $query->orWhere('username', $data['email']);
                }
            },
            'password' => $data['password'],
            'is_active' => true,
        ];
    }
}