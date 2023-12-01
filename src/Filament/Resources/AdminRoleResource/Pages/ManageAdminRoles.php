<?php

namespace Green\AdminAuth\Filament\Resources\AdminRoleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Green\AdminAuth\Filament\Resources\AdminRoleResource;

/**
 * ロールの管理画面
 */
class ManageAdminRoles extends ManageRecords
{
    protected static string $resource = AdminRoleResource::class;

    /**
     * ヘッダーのアクションを取得する
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('xl')->slideOver()
                ->createAnother(false),
        ];
    }
}
