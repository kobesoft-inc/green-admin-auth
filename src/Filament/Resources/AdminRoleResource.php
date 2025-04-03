<?php

namespace Green\AdminAuth\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Green\AdminAuth\Facades\PermissionRegistry;
use Green\AdminAuth\Filament\Resources\AdminRoleResource\Pages\ManageAdminRoles;
use Green\AdminAuth\Models\AdminRole;
use Green\AdminAuth\GreenAdminAuthPlugin;
use Green\ResourceModule\Facades\ModuleRegistry;
use Illuminate\Database\Eloquent\Model;

/**
 * ロールのリソース
 */
class AdminRoleResource extends Resource
{
    protected static ?string $model = AdminRole::class;
    protected static ?string $navigationIcon = 'heroicon-s-lock-closed';
    protected static ?int $navigationSort = 1300;

    /**
     * ナビゲーションのグループ
     *
     * @return string|null
     */
    public static function getNavigationGroup(): ?string
    {
        return GreenAdminAuthPlugin::get()->getNavigationGroup();
    }

    /**
     * モデルの名前
     *
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __('green::admin-auth.admin-role.model');
    }

    /**
     * フォームを構築
     *
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        $form = $form
            ->schema([
                // 名前
                Forms\Components\TextInput::make('name')
                    ->label(__('green::admin-auth.admin-group.name'))
                    ->required()->maxLength(20),
                // 権限
                self::makePermissionsComponent('permissions'),
            ])
            ->columns(1);
        return ModuleRegistry::apply(static::class, $form);
    }

    /**
     * テーブルを構築
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        $table = $table
            ->columns([
                // 名前
                Tables\Columns\TextColumn::make('name')
                    ->label(__('green::admin-auth.admin-role.name'))
                    ->sortable()->searchable()->toggleable(),

                // 割り当てられたユーザー
                GreenAdminAuthPlugin::get()->isAvatarDisabled()
                    ?
                    Tables\Columns\TextColumn::make('users.name')
                        ->label(GreenAdminAuthPlugin::get()->getUserModelLabel())
                        ->limit(20)
                        ->toggleable()
                    :
                    Tables\Columns\ImageColumn::make('users.avatar_url')
                        ->label(GreenAdminAuthPlugin::get()->getUserModelLabel())
                        ->circular()->overlap(5)->limit(5)->limitedRemainingText()
                        ->toggleable(),

                // 割り当てられたグループ
                Tables\Columns\TextColumn::make('groups.name')
                    ->label(GreenAdminAuthPlugin::get()->getGroupModelLabel())
                    ->badge()
                    ->toggleable()
                    ->hidden(GreenAdminAuthPlugin::get()->isGroupDisabled()),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // 編集
                    Tables\Actions\EditAction::make()
                        ->modalWidth('xl')->slideOver(),
                    // 削除
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
        return ModuleRegistry::apply(static::class, $table);
    }

    /**
     * ページを返す
     *
     * @return array|PageRegistration[]
     */
    public static function getPages(): array
    {
        return [
            'index' => ManageAdminRoles::route('/'),
        ];
    }

    /**
     * 削除できるか？
     *
     * @param Model $record
     * @return bool
     */
    public static function canDelete(Model $record): bool
    {
        // ユーザーとグループがなければ削除できる
        assert($record instanceof AdminRole);
        return $record->users()->count() == 0;
    }

    /**
     * パーミッションを選択するコンポーネントを生成する
     *
     * @param string $name
     * @return Component
     */
    private static function makePermissionsComponent(string $name): Forms\Components\Component
    {
        return Forms\Components\Group::make(
            PermissionRegistry::getGroups()
                ->map(function (string $group) use ($name) {
                    return Forms\Components\CheckboxList::make($name)
                        ->label($group)
                        ->options(PermissionRegistry::getOptions($group))
                        ->columns(2)->gridDirection('row');
                })
                ->toArray()
        );
    }
}
