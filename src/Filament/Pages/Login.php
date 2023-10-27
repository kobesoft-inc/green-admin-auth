<?php

namespace Green\AdminBase\Filament\Pages;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;

class Login extends \Filament\Pages\Auth\Login
{
    /**
     * ユーザー名またはメールアドレスの入力フォームを取得する
     *
     * @return Component
     */
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('green::admin_base.pages.login.username_or_email'))
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
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
                $query->where('email', $data['email'])->orWhere('username', $data['email']);
            },
            'password' => $data['password'],
            'is_active' => true,
        ];
    }
}