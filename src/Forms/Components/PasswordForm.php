<?php

namespace Green\AdminBase\Forms\Components;

use Closure;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Green\AdminBase\Mail\PasswordReset;
use Green\AdminBase\Models\AdminUser;
use Green\AdminBase\Plugin;
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
     * @param  array|Closure  $schema
     * @return PasswordForm
     */
    public static function make(array|Closure $schema = []): static
    {
        return app(static::class)
            ->schema([
                // パスワードを生成するか？
                Forms\Components\Checkbox::make('generate_password')
                    ->label(__('green::admin_base.admin_user.generate_password'))
                    ->default(true)
                    ->live()
                    ->afterStateUpdated(function (?int $state, Set $set) {
                        if ($state) {
                            $set('send_password', true);
                        }
                    }),

                // パスワード
                \Phpsa\FilamentPasswordReveal\Password::make('password')
                    ->label(__('green::admin_base.admin_user.password'))
                    ->password()
                    ->showIcon('bi-eye')->hideIcon('bi-eye-slash')
                    ->visible(fn(Get $get): bool => !$get('generate_password'))
                    ->required()->ascii()->minLength(Plugin::get()->getPasswordMinLength()),

                // パスワードの変更を要求するか？
                Forms\Components\Checkbox::make('require_change_password')
                    ->label(__('green::admin_base.admin_user.require_change_password'))
                    ->default(true),

                // パスワードをメールで送信するか？
                Forms\Components\Checkbox::make('send_password')
                    ->label(__('green::admin_base.admin_user.send_password'))
                    ->default(true)
                    ->live()
                    ->disabled(fn(Get $get): bool => $get('generate_password'))
                    ->rule(fn(Get $get) => function (string $attribute, $value, Closure $fail) use ($get) {
                        if (blank($get('email'))) {
                            $fail(__('green::admin_base.validations.email_is_not_set'));
                        }
                    }),
            ]);
    }


    /**
     * パスワードの生成・有効期限設定・メール送信の処理を行う
     *
     * @param  array  $data  入力データ
     * @param  AdminUser|null  $adminUser  モデル（作成済みの場合）
     * @return array
     */
    static public function process(array $data, ?AdminUser $adminUser): array
    {
        // パスワードを生成する
        if ($data['generate_password']) {
            $data['password'] = Str::password(Plugin::get()->getGeneratedPasswordLength());
        }

        // 次回ログイン時にパスワード変更を要求する
        if ($data['require_change_password']) {
            $data['password_expire_at'] = Carbon::now();
        }

        // パスワードをユーザーのメールに送信する
        if ($data['generate_password'] || $data['send_password']) {
            $email = $adminUser?->email ?? $data['email'];
            $username = $adminUser?->username ?? $data['username'];
            $login = filament()->getCurrentPanel()->getLoginUrl();
            Mail::to($email)->send(new PasswordReset(
                email: $email,
                username: $username,
                password: $data['password'],
                login: $login
            ));
        }

        return $data;
    }
}
