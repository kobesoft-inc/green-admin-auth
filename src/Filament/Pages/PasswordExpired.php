<?php

namespace Green\AdminAuth\Filament\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Green\AdminAuth\Forms\Components\Password;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * パスワードの有効期限切れのページ
 */
class PasswordExpired extends SimplePage
{
    const PASSWORD_EXPIRED_USER_ID = 'password-expired-user-id';

    protected static string $view = 'green::filament.pages.password-expired';
    public ?array $data = [];

    /**
     * コンポーネントの初期化
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function mount(): void
    {
        if (!$this->getUserId()) {
            $this->redirectToLogin();
        }
        $this->form->fill();
    }

    /**
     * フォーム送信時の処理
     *
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ValidationException
     */
    public function changePassword(): mixed
    {
        if (!($userId = $this->getUserId())) {
            return $this->redirectToLogin();
        }

        $data = $this->form->getState();

        $user = Filament::auth()->getProvider()->retrieveById($userId);
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'data.current_password' => __('green::admin-auth.pages.password-expired.incorrect-password'),
            ]);
        }

        // パスワードを更新する
        $user->password = $data['new_password'];
        $user->save();

        // パスワードの有効期限切れのセッションを開放
        session()->forget(self::PASSWORD_EXPIRED_USER_ID);
        session()->regenerate();

        // 通知を表示
        Notification::make()
            ->title(__('green::admin-auth.pages.password-expired.password-changed'))
            ->success()
            ->send();

        // ログイン画面に遷移する
        return $this->redirectToLogin();
    }

    /**
     * フォームを構築
     *
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // 古いパスワード
                TextInput::make('current_password')
                    ->label(__('green::admin-auth.pages.password-expired.current-password'))
                    ->password()->required(),

                // 新しいパスワード
                Password::make('new_password')
                    ->label(__('green::admin-auth.pages.password-expired.new-password'))
                    ->different('current_password')
                    ->required(),
            ])
            ->statePath('data');
    }

    /**
     * 見出し
     *
     * @return string|Htmlable
     */
    public function getHeading(): string|Htmlable
    {
        return __('green::admin-auth.pages.password-expired.heading');
    }

    /**
     * 小見出し
     *
     * @return string|Htmlable
     */
    public function getSubheading(): string|Htmlable
    {
        return __('green::admin-auth.pages.password-expired.subheading');
    }

    /**
     * フォームのアクション
     *
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('password-expired')
                ->label(__('green::admin-auth.pages.password-expired.change-password'))
                ->submit('password-expired'),
        ];
    }

    /**
     * 全幅のフォームアクションを使用する
     *
     * @return bool
     */
    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    /**
     * ログインしようとしたユーザーIDを取得する
     *
     * @return int|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getUserId(): ?int
    {
        return session()->get(self::PASSWORD_EXPIRED_USER_ID, null);
    }

    /**
     * ログインページにリダイレクトする
     */
    protected function redirectToLogin(): mixed
    {
        return redirect(filament()->getCurrentPanel()->getLoginUrl());
    }
}
