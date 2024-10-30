<?php

namespace Green\AdminAuth\Forms\Components;

use Closure;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Green\AdminAuth\Mail\PasswordReset;
use Green\AdminAuth\GreenAdminAuthPlugin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * パスワードの入力・生成フォーム
 *
 * 管理ユーザー作成、パスワードのリセットフォームから参照されている
 */
class PasswordForm extends Forms\Components\Group
{
    /**
     * パスワードのリセットフォームを返す
     *
     * @param array|Closure $schema
     * @return PasswordForm
     */
    public static function make(array|Closure $schema = []): static
    {
        return app(static::class)
            ->schema([
                // パスワードを生成するか？
                Forms\Components\Checkbox::make('generate_password')
                    ->label(__('green::admin-auth.admin-user.generate-password'))
                    ->default(true)
                    ->live()
                    ->afterStateUpdated(function (?int $state, Set $set) {
                        if ($state) {
                            $set('send_password', true);
                        }
                    })
                    ->hidden(GreenAdminAuthPlugin::get()->isEmailDisabled()),

                // パスワード
                Password::make('password')
                    ->label(__('green::admin-auth.admin-user.password'))
                    ->showIcon('bi-eye')->hideIcon('bi-eye-slash')
                    ->visible(fn(Get $get): bool => !$get('generate_password') || GreenAdminAuthPlugin::get()->isEmailDisabled())
                    ->required(),

                // パスワードの変更を要求するか？
                Forms\Components\Checkbox::make('force_password_change')
                    ->label(__('green::admin-auth.admin-user.force-change-password'))
                    ->default(true),

                // パスワードをメールで送信するか？
                Forms\Components\Checkbox::make('send_password')
                    ->label(__('green::admin-auth.admin-user.send-password'))
                    ->default(true)
                    ->live()
                    ->disabled(fn(Get $get): bool => $get('generate_password'))
                    ->rule(fn(Get $get) => function (string $attribute, $value, Closure $fail) use ($get) {
                        if ($get('send_password') && blank($get('email'))) {
                            $fail(__('green::admin-auth.validations.email-required'));
                        }
                    })
                    ->hidden(GreenAdminAuthPlugin::get()->isEmailDisabled()),
            ]);
    }

    /**
     * パスワードの生成・有効期限設定・メール送信の処理を行う
     *
     * @param array $data 入力データ
     * @param Model|null $user モデル（作成済みの場合）
     * @param string|null $panelId パネルのID
     * @return array
     */
    static public function process(array $data, ?Model $user, ?string $panelId = null): array
    {
        // パスワードを生成する
        if (Arr::get($data, 'generate_password', false)) {
            $data['password'] = Str::password(GreenAdminAuthPlugin::get()->getGeneratedPasswordLength());
            $data['send_password'] = true;
        }

        // 次回ログイン時にパスワード変更を要求する
        if (Arr::get($data, 'force_password_change', false)) {
            $data['password_expires_at'] = Carbon::now();
        }

        // パスワードをユーザーのメールに送信する
        if (Arr::get($data, 'send_password', false)) {
            $email = $user?->email ?? $data['email'];
            Mail::to($email)->send(new PasswordReset(
                email: $email,
                username: $user?->username ?? $data['username'] ?? null,
                password: $data['password'],
                login: self::getLoginUrl($panelId)
            ));
        }

        return $data;
    }

    /**
     * ログインURLを取得する
     *
     * @param string|null $panelId パネルのID
     * @return string
     */
    static protected function getLoginUrl(?string $panelId): string
    {
        return ($panelId ? filament()->getPanel($panelId) : filament()->getCurrentPanel())->getLoginUrl();
    }
}
