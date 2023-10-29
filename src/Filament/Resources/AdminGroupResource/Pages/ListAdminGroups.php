<?php

namespace Green\AdminBase\Filament\Resources\AdminGroupResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Green\AdminBase\Filament\Resources\AdminGroupResource;

class ListAdminGroups extends ListRecords
{
    protected static string $resource = AdminGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('md')
                ->createAnother(false),
        ];
    }
}
