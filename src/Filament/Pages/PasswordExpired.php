<?php

namespace Green\AdminBase\Filament\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SimplePage;
use Green\AdminBase\Models\AdminUser;
use Green\AdminBase\Plugin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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

        /** @var AdminUser $user */
        $user = AdminUser::findOrFail($userId);
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'data.current_password' => __('green::admin_base.pages.password_expired.invalid_password'),
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
            ->title('UNKO')
            ->danger()
            ->send();

        // ログイン画面に遷移する
        return $this->redirectToLogin();
    }

    /**
     * フォームを構築
     *
     * @param  Form  $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // 古いパスワード
                TextInput::make('current_password')
                    ->label(__('green::admin_base.pages.password_expired.current_password'))
                    ->password()->required(),
                // 新しいパスワード
                \Phpsa\FilamentPasswordReveal\Password::make('new_password')
                    ->label(__('green::admin_base.pages.password_expired.new_password'))
                    ->password()
                    ->different('current_password')
                    ->required()->ascii()->minLength(Plugin::get()->getPasswordMinLength()),
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
        return __('green::admin_base.pages.password_expired.heading');
    }

    /**
     * 小見出し
     *
     * @return string|Htmlable
     */
    public function getSubheading(): string|Htmlable
    {
        return __('green::admin_base.pages.password_expired.subheading');
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
                ->label(__('green::admin_base.pages.password_expired.change_password'))
                ->submit('password-expired'),
        ];
    }

    /**
     * 全幅のフォームアクション？
     *
     * @return bool
     */
    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    /**
     * パスワードの有効期限が切れた
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