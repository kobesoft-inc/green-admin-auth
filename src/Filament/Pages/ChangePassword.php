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
use Green\AdminAuth\Forms\Components\Password;
use Green\AdminAuth\Models\AdminUser;
use Green\AdminAuth\GreenAdminAuthPlugin;
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
    public ?array $data = [];

    /**
     * フォーム送信時の処理
     *
     * @return void
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
                Password::make('new_password')
                    ->label(__('green::admin-auth.pages.password-expired.new-password'))
                    ->different('current_password')
                    ->required(),
            ])
            ->statePath('data');
    }

    /**
     * 見出し
     */
    public function getHeading(): string|Htmlable
    {
        return ''; // Pageコンポーネントの見出しを表示しない
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
