<?php

namespace Green\AdminBase\Filament\Resources\AdminUserResource\Actions;

use Filament\Forms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Green\AdminBase\Filament\Resources\AdminUserResource\Forms\PasswordForm;
use Green\AdminBase\Models\AdminUser;
use Illuminate\Database\Eloquent\Model;

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
            PasswordForm::form(),
        ]);

        $this->action(function (array $data, AdminUser $record) {
            $data = PasswordForm::process($data, $record);
            $record->fill($data)->save();
            $this->sendSuccessNotification();
        });
    }
}