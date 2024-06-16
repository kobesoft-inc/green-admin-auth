<?php

namespace Green\AdminAuth\Filament\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Pages\SimplePage;
use Green\AdminAuth\Models\AdminUser;
use Green\AdminAuth\Plugin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * パスワードの変更のページ
 */
class ChangePassword extends Page
{
    protected static string $view = 'green::filament.pages.change-password';
    protected static bool $shouldRegisterNavigation = false;
    protected ?string $heading = null;
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
    }

    /**
     * フォーム送信時の処理
     *
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ValidationException
     */
    public function changePassword(): void
    {
        $data = $this->form->getState();

        // 入力をクリアする
        $this->form->fill([]);

        /** @var AdminUser $user */
        $user = auth()->user();
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'data.current_password' => __('green::admin-auth.pages.password-expired.incorrect-password'),
            ]);
        }

        // パスワードを更新する
        $user->password = $data['new_password'];
        $user->save();

        // ホーム画面にリダイレクトする
        $this->redirect(Filament::getHomeUrl());
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
                \Phpsa\FilamentPasswordReveal\Password::make('new_password')
                    ->label(__('green::admin-auth.pages.password-expired.new-password'))
                    ->password()
                    ->different('current_password')
                    ->required()->ascii()->minLength(Plugin::get()->getPasswordMinLength()),
            ])
            ->statePath('data');
    }

    /**
     * 見出し
     */
    public function getHeading(): string|Htmlable
    {
        return '';
    }

    /**
     * フォームのアクション
     *
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('change-password')
                ->label(__('green::admin-auth.pages.password-expired.change-password'))
                ->submit('change-password'),
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
}
