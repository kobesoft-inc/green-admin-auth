<?php

namespace Green\AdminBase\Filament\Resources\AdminUserResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Green\AdminBase\Filament\Resources\AdminUserResource;

class ListAdminUsers extends ListRecords
{
    protected static string $resource = AdminUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('md')->slideOver()
                ->createAnother(false),
        ];
    }
}
