<?php

namespace Green\AdminAuth\Filament\Resources\AdminUserResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Green\AdminAuth\Filament\Resources\AdminUserResource;
use Green\AdminAuth\Forms\Components\PasswordForm;
use Green\AdminAuth\Models\AdminUser;

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
                    $data = PasswordForm::process($data, null);
                    return AdminUser::create($data);
                }),
        ];
    }
}
