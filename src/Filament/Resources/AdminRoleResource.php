<?php

namespace Green\AdminAuth\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Green\AdminAuth\Facades\PermissionManager;
use Green\AdminAuth\Filament\Resources\AdminRoleResource\Pages\ListAdminRoles;
use Green\AdminAuth\Models\AdminRole;
use Green\AdminAuth\Plugin;
use Illuminate\Database\Eloquent\Model;

class AdminRoleResource extends Resource
{
    protected static ?string $model = AdminRole::class;
    protected static ?string $navigationIcon = 'bi-person-lock';
    protected static ?int $navigationSort = 1300;

    /**
     * ナビゲーションのグループ
     *
     * @return string|null
     */
    public static function getNavigationGroup(): ?string
    {
        return __('green::admin-auth.navigation-group');
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
        return $form
            ->schema([
                // 名前
                Forms\Components\TextInput::make('name')
                    ->label(__('green::admin-auth.admin-group.name'))
                    ->required()->maxLength(20),
                // 権限
                self::makePermissionsComponent('permissions'),
            ])
            ->columns(1);
    }

    /**
     * テーブルを構築
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 名前
                Tables\Columns\TextColumn::make('name')
                    ->label(__('green::admin-auth.admin-role.name'))
                    ->sortable()->searchable()->toggleable(),

                // 割り当てられたユーザー
                Tables\Columns\ImageColumn::make('users.avatar_url')
                    ->label(Plugin::get()->getUserModelLabel())
                    ->circular()->overlap(5)->limit(5)->limitedRemainingText()
                    ->toggleable(),

                // 割り当てられたグループ
                Tables\Columns\TextColumn::make('groups.name')
                    ->label(Plugin::get()->getGroupModelLabel())
                    ->toggleable()
                    ->hidden(Plugin::get()->isGroupDisabled()),
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
    }

    /**
     * ページを返す
     *
     * @return array|PageRegistration[]
     */
    public static function getPages(): array
    {
        return [
            'index' => ListAdminRoles::route('/'),
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
            PermissionManager::getGroups()
                ->map(function (string $group) use ($name) {
                    return Forms\Components\CheckboxList::make($name)
                        ->label($group)
                        ->options(PermissionManager::getOptions($group))
                        ->columns(2)->gridDirection('row');
                })
                ->toArray()
        );
    }
}
