<?php

namespace Green\AdminAuth\Filament\Resources\AdminGroupResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Green\AdminAuth\Filament\Resources\AdminGroupResource;

/**
 * グループの管理画面
 */
class ManageAdminGroups extends ManageRecords
{
    protected static string $resource = AdminGroupResource::class;

    /**
     * ヘッダーのアクションを取得する
     *
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('md')
                ->createAnother(false),
        ];
    }
}
