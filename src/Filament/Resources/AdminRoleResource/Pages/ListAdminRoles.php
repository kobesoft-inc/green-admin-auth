<?php

namespace Green\AdminAuth\Filament\Resources\AdminRoleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Green\AdminAuth\Filament\Resources\AdminRoleResource;

class ListAdminRoles extends ListRecords
{
    protected static string $resource = AdminRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('xl')->slideOver()
                ->createAnother(false),
        ];
    }
}
