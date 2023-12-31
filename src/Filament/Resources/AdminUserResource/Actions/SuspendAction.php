<?php

namespace Green\AdminAuth\Filament\Resources\AdminUserResource\Actions;

use Filament\Tables\Actions\Action;

/**
 * 管理ユーザーを停止する
 */
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

        $this->label(__('green::admin-auth.admin-user.actions.suspend'));
        $this->icon('heroicon-o-x-circle');
        $this->requiresConfirmation();
        $this->successNotificationTitle(__('green::admin-auth.admin-user.actions.suspend-succeed'));

        $this->action(function ($record) {
            $record->is_active = false;
            $record->save();
            $this->sendSuccessNotification();
        });
    }
}
