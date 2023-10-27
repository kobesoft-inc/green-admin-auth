<?php

namespace Green\AdminBase\Filament\Resources\AdminUserResource\Actions;

use Filament\Tables\Actions\Action;

class ResumeAction extends Action
{
    /**
     * アクションの名前
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return 'resume';
    }

    /**
     * アクションのセットアップ
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('green::admin_base.admin_user.actions.resume'));
        $this->icon('heroicon-o-check-circle');
        $this->requiresConfirmation();
        $this->successNotificationTitle(__('green::admin_base.admin_user.actions.resume_succeed'));

        $this->action(function ($record) {
            $record->is_active = true;
            $record->save();
            $this->sendSuccessNotification();
        });
    }
}