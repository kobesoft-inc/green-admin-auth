<?php

namespace Green\AdminBase\Filament\Resources\AdminUserResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Green\AdminBase\Filament\Resources\AdminUserResource;
use Green\AdminBase\Models\AdminUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ListAdminUsers extends ListRecords
{
    protected static string $resource = AdminUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('md')->slideOver()
                ->createAnother(false)
                ->using(function (array $data): AdminUser {
                    $data = AdminUserResource::processPasswordForm($data, null);
                    return AdminUser::create($data);
                }),
        ];
    }
}
