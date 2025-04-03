<?php

namespace Green\AdminAuth\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Green\AdminAuth\Filament\Resources\AdminGroupResource\Pages\ManageAdminGroups;
use Green\AdminAuth\Models\AdminGroup;
use Green\AdminAuth\Models\AdminRole;
use Green\AdminAuth\GreenAdminAuthPlugin;
use Green\AdminAuth\Rules\NodeParent;
use Green\ResourceModule\Facades\ModuleRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * グループのリソース
 */
class AdminGroupResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicons-s-users';
    protected static ?int $navigationSort = 1200;

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
     * モデルのクラス
     */
    public static function getModel(): string
    {
        return GreenAdminAuthPlugin::get()->getGroupModel();
    }

    /**
     * モデルの名前
     *
     * @return string
     */
    public static function getModelLabel(): string
    {
        return GreenAdminAuthPlugin::get()->getGroupModelLabel();
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

                // 親のグループ
                Forms\Components\Select::make('parent_id')
                    ->label(__('green::admin-auth.admin-group.parent-id', GreenAdminAuthPlugin::get()->getTranslationWords()))
                    ->options(AdminGroup::getOptions())
                    ->native(false)->allowHtml(true)
                    ->rules([fn($record) => new NodeParent(record: $record)]),

                // ロール
                Forms\Components\Select::make('roles')
                    ->label(__('green::admin-auth.admin-user.roles'))
                    ->relationship('roles', 'name')
                    ->options(AdminRole::getOptions())->multiple()
                    ->placeholder('')
                    ->native(false),
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
                    ->label(__('green::admin-auth.admin-group.name'))
                    ->extraAttributes(fn($record) => ['style' => 'text-indent:' . $record->depth . 'em'])
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

                // 割り当てられたロール
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('green::admin-auth.admin-group.roles'))
                    ->badge()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // 編集
                    Tables\Actions\EditAction::make()
                        ->modalWidth('md'),
                    // 削除
                    Tables\Actions\DeleteAction::make(),
                ])
            ]);
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
            'index' => ManageAdminGroups::route('/'),
        ];
    }

    /**
     * クエリを返す
     *
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->defaultOrder()->withDepth();
    }

    /**
     * 削除できるか？
     *
     * @param Model $record
     * @return bool
     */
    public static function canDelete(Model $record): bool
    {
        // 所属ユーザーと子グループがなければ削除できる
        assert($record instanceof AdminGroup);
        return $record->users()->count() == 0 && $record->children()->count() == 0;
    }
}
