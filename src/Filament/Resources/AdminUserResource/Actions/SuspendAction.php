<?php

namespace Green\AdminAuth\Filament\Resources\AdminUserResource\Actions;

use Filament\Tables\Actions\Action;

class SuspendAction extends Action
{
    /**
     * アクションの名前
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return 'suspend';
    }

    /**
     * アクションのセットアップ
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('green::admin_base.admin_user.actions.suspend'));
        $this->icon('heroicon-o-x-circle');
        $this->requiresConfirmation();
        $this->successNotificationTitle(__('green::admin_base.admin_user.actions.suspend_succeed'));

        $this->action(function ($record) {
            $record->is_active = false;
            $record->save();
            $this->sendSuccessNotification();
        });
    }
}