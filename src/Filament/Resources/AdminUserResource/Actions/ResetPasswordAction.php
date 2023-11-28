<?php

namespace Green\AdminAuth\Filament\Resources\AdminUserResource\Actions;

use Filament\Forms;
use Filament\Tables\Actions\Action;
use Green\AdminAuth\Forms\Components\PasswordForm;
use Green\AdminAuth\Models\AdminUser;

class ResetPasswordAction extends Action
{
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

        $this->label(__('green::admin_base.admin_user.actions.reset_password'));

        $this->modalHeading($this->getLabel());

        $this->icon('bi-key');

        $this->successNotificationTitle(__('green::admin_base.admin_user.actions.reset_password_succeed'));

        $this->form([
            Forms\Components\TextInput::make('email')
                ->hidden()
                ->default(fn(AdminUser $record) => $record->email),
            PasswordForm::make(),
        ]);

        $this->action(function (array $data, AdminUser $record) {
            $data = PasswordForm::process($data, $record);
            $record->fill($data)->save();
            $this->sendSuccessNotification();
        });
    }
}