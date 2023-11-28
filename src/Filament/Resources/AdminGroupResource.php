<?php

namespace Green\AdminAuth\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Green\AdminAuth\Filament\Resources\AdminGroupResource\Pages\ListAdminGroups;
use Green\AdminAuth\Models\AdminGroup;
use Green\AdminAuth\Models\AdminRole;
use Green\AdminAuth\Plugin;
use Green\AdminAuth\Rules\NodeParent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AdminGroupResource extends Resource
{
    protected static ?string $navigationIcon = 'bi-people';
    protected static ?int $navigationSort = 1200;

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
     * モデルのクラス
     */
    public static function getModel(): string
    {
        return Plugin::get()->getGroupModel();
    }

    /**
     * モデルの名前
     *
     * @return string
     */
    public static function getModelLabel(): string
    {
        return Plugin::get()->getGroupModelLabel();
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

                // 親のグループ
                Forms\Components\Select::make('parent_id')
                    ->label(__('green::admin-auth.admin-group.parent-id', Plugin::get()->getTranslationWords()))
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
                    ->label(__('green::admin-auth.admin-group.name'))
                    ->extraAttributes(fn($record) => ['style' => 'text-indent:' . $record->depth . 'em'])
                    ->sortable()->searchable()->toggleable(),

                // 割り当てられたユーザー
                Tables\Columns\ImageColumn::make('users.avatar_url')
                    ->label(Plugin::get()->getUserModelLabel())
                    ->circular()->overlap(5)->limit(5)->limitedRemainingText()
                    ->toggleable(),

                // 割り当てられたロール
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('green::admin-auth.admin-group.roles'))
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
    }

    /**
     * ページを返す
     *
     * @return array|PageRegistration[]
     */
    public static function getPages(): array
    {
        return [
            'index' => ListAdminGroups::route('/'),
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
