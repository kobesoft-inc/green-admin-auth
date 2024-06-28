<?php

namespace Green\AdminAuth\Filament\Resources\AdminUserResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Green\AdminAuth\Filament\Resources\AdminUserResource;
use Green\AdminAuth\Forms\Components\PasswordForm;
use Green\AdminAuth\Models\AdminUser;
use Green\AdminAuth\GreenAdminAuthPlugin;

/**
 * 管理ユーザーの一覧ページ
 */
class ManageAdminUsers extends ManageRecords
{
    protected static string $resource = AdminUserResource::class;

    /**
     * ヘッダーのアクションを取得する
     *
     * @return array|Actions\Action[]
     */
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

    /**
     * 管理ユーザーのタブを取得する
     *
     * @return array|Tab[]
     */
    public function getTabs(): array
    {
        return GreenAdminAuthPlugin::get()->getUserTabs();
    }
}
