<?php

namespace Green\AdminBase\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Green\AdminBase\Filament\Resources\AdminUserResource\Pages\ListAdminUsers;
use Green\AdminBase\Models\AdminGroup;
use Green\AdminBase\Models\AdminRole;
use Green\AdminBase\Models\AdminUser;
use Green\AdminBase\Permissions\ManageAdminUser;
use Green\AdminBase\Permissions\ManageAdminUserInGroup;
use Illuminate\Database\Eloquent\Builder;
use Phpsa\FilamentPasswordReveal\Password;

class AdminUserResource extends Resource
{
    protected static ?string $model = AdminUser::class;
    protected static ?string $navigationIcon = 'bi-person';

    /**
     * ナビゲーションのグループ
     *
     * @return string|null
     */
    public static function getNavigationGroup(): ?string
    {
        return __('green::admin_base.navigation_group');
    }

    /**
     * モデルの名前
     *
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __('green::admin_base.admin_user.model');
    }

    /**
     * フォームを構築
     *
     * @param  Form  $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // アバター
                Forms\Components\FileUpload::make('avatar')
                    ->label(__('green::admin_base.admin_user.avatar'))
                    ->hiddenLabel()
                    ->avatar()
                    ->alignCenter(),
                // 名前
                Forms\Components\TextInput::make('name')
                    ->label(__('green::admin_base.admin_user.name'))
                    ->required()->maxLength(20),
                // メール
                Forms\Components\TextInput::make('email')
                    ->label(__('green::admin_base.admin_user.email'))
                    ->email()->maxLength(100),
                // ユーザー名
                Forms\Components\TextInput::make('username')
                    ->label(__('green::admin_base.admin_user.username'))
                    ->requiredWithout('email')->ascii()->alphaDash()
                    ->unique('admin_users', 'username', fn(?AdminUser $record) => $record),
                // パスワード
                \Phpsa\FilamentPasswordReveal\Password::make('password')
                    ->label(__('green::admin_base.admin_user.password'))
                    ->showIcon('bi-eye')->hideIcon('bi-eye-slash')
                    ->password()->ascii()
                    ->visibleOn('create'),
                // グループ
                Forms\Components\Select::make('groups')
                    ->label(__('green::admin_base.admin_user.groups'))
                    ->relationship('groups', 'name')
                    ->options(self::getGroupOptions(true))
                    ->multiple(config('green.admin_base.users_can_belong_to_multiple_groups'))
                    ->allowHtml()->native(false)->placeholder('')
                    ->requiredWithout('roles'),
                // ロール
                Forms\Components\Select::make('roles')
                    ->label(__('green::admin_base.admin_user.roles'))
                    ->relationship('roles', 'name')
                    ->options(AdminRole::getOptions())
                    ->multiple(config('green.admin_base.multiple_roles_can_be_assigned_to_users'))
                    ->native(false)->placeholder('')
                    ->visible(auth()->user()->hasPermission(\Green\AdminBase\Permissions\EditAdminUserRole::class)),
            ])
            ->columns(1);
    }

    /**
     * テーブルを構築
     *
     * @param  Table  $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 名前
                Tables\Columns\TextColumn::make('name')
                    ->label(__('green::admin_base.admin_user.name'))
                    ->sortable()->searchable()->toggleable(),
                // メール
                Tables\Columns\TextColumn::make('email')
                    ->label(__('green::admin_base.admin_user.email'))
                    ->sortable()->searchable()->toggleable(),
                // ユーザー名
                Tables\Columns\TextColumn::make('username')
                    ->label(__('green::admin_base.admin_user.username'))
                    ->sortable()->searchable()->toggleable(),
                // 状態
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('green::admin_base.admin_user.is_active'))
                    ->boolean()
                    ->sortable()->toggleable(),
                // 管理グループ
                Tables\Columns\TextColumn::make('groups.name')
                    ->label(__('green::admin_base.admin_user.groups'))
                    ->sortable()->toggleable(),
                // 管理ロール
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('green::admin_base.admin_user.roles'))
                    ->sortable()->toggleable(),
                // 最終ログイン
                Tables\Columns\TextColumn::make('login_logs_max_created_at')
                    ->label(__('green::admin_base.admin_user.last_login_at'))
                    ->max('loginLogs', 'created_at')
                    ->since()
                    ->sortable()->toggleable(),
                // 作成日時
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('green::admin_base.admin_user.created_at'))
                    ->sortable()->toggleable()->toggledHiddenByDefault(),
                // 更新日時
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('green::admin_base.admin_user.updated_at'))
                    ->sortable()->toggleable()->toggledHiddenByDefault(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // 編集
                    Tables\Actions\EditAction::make()
                        ->modalWidth('md')->slideOver(),
                    // ログインを停止
                    AdminUserResource\Actions\SuspendAction::make()
                        ->visible(fn($record) => $record->is_active && auth()->user()->can('suspend', $record)),
                    // ログインを再開
                    AdminUserResource\Actions\ResumeAction::make()
                        ->visible(fn($record) => !$record->is_active),
                    // 削除
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => ListAdminUsers::route('/'),
        ];
    }

    /**
     * クエリを返す
     *
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(self::hasInGroupOnlyPermission(), function ($query) {
                $query->inGroups(auth()->user()->groupsWithDescendants());
            });
    }

    /**
     * 権限がグループ内のユーザー管理権限のみであるか？
     *
     * @return bool
     */
    private static function hasInGroupOnlyPermission(): bool
    {
        /** @var AdminUser $user */
        $user = auth()->user();
        return !$user->hasPermission(ManageAdminUser::class)
            && $user->hasPermission(ManageAdminUserInGroup::class);
    }

    /**
     * グループの選択肢を取得する
     *
     * @param  bool  $html
     * @return array
     */
    private static function getGroupOptions(bool $html): array
    {
        if (self::hasInGroupOnlyPermission()) {
            return collect(AdminGroup::getOptions($html))
                ->intersectByKeys(auth()->user()->groupsWithDescendants()->pluck('id')->flip())
                ->toArray();
        } else {
            return AdminGroup::getOptions($html);
        }
    }
}