<?php

namespace Green\AdminAuth\Filament\Resources\AdminUserResource\Actions;

use Filament\Forms;
use Filament\Tables\Actions\Action;
use Green\AdminAuth\Forms\Components\PasswordForm;
use Illuminate\Database\Eloquent\Model;

/**
 * 管理ユーザーのパスワードをリセットする
 */
class ResetPasswordAction extends Action
{
    protected ?string $panelId = null;

    /**
     * アクションの名前
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return 'resetPassword';
    }

    /**
     * アクションのセットアップ
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('green::admin-auth.admin-user.actions.reset-password'));

        $this->modalHeading($this->getLabel());

        $this->icon('heroicons-s-key');

        $this->successNotificationTitle(__('green::admin-auth.admin-user.actions.reset-password-succeed'));

        $this->form([
            Forms\Components\TextInput::make('email')
                ->hidden()
                ->default(fn(Model $record) => $record->email),
            PasswordForm::make(),
        ]);

        $this->action(function (array $data, Model $record) {
            $data = PasswordForm::process($data, $record, $this->getPanelId());
            $record->fill($data)->save();
            $this->sendSuccessNotification();
        });
    }

    /**
     * ログインのためのパネルのIDを設定する
     *
     * @param string $panelId
     * @return $this
     */
    public function panelId(string $panelId): self
    {
        $this->panelId = $panelId;
        return $this;
    }

    /**
     * ログインのためのパネルのIDを取得する
     *
     * @return string|null
     */
    public function getPanelId(): ?string
    {
        return $this->panelId;
    }
}
