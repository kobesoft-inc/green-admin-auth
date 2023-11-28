<?php

namespace Green\AdminAuth\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Green\AdminAuth\Filament\Resources\AdminUserResource\Pages\ListAdminUsers;
use Green\AdminAuth\Models\AdminGroup;
use Green\AdminAuth\Models\AdminRole;
use Green\AdminAuth\Models\AdminUser;
use Green\AdminAuth\Permissions\ManageAdminUser;
use Green\AdminAuth\Permissions\ManageAdminUserInGroup;
use Green\AdminAuth\Plugin;
use Illuminate\Database\Eloquent\Builder;

class AdminUserResource extends Resource
{
    protected static ?string $navigationIcon = 'bi-person';
    protected static ?int $navigationSort = 1100;

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
        return Plugin::get()->getUserModel();
    }

    /**
     * モデルの名前
     *
     * @return string
     */
    public static function getModelLabel(): string
    {
        return Plugin::get()->getUserModelLabel();
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
                // アバター
                Forms\Components\FileUpload::make('avatar')
                    ->label(__('green::admin-auth.admin-user.avatar'))
                    ->hiddenLabel()
                    ->avatar()
                    ->alignCenter()
                    ->hidden(Plugin::get()->isAvatarDisabled()),

                // 名前
                Forms\Components\TextInput::make('name')
                    ->label(__('green::admin-auth.admin-user.name'))
                    ->required()->maxLength(20),

                // メール
                Forms\Components\TextInput::make('email')
                    ->label(__('green::admin-auth.admin-user.email'))
                    ->required(Plugin::get()->isUsernameDisabled())
                    ->email()->maxLength(100)
                    ->hidden(Plugin::get()->isEmailDisabled()),

                // ユーザー名
                Forms\Components\TextInput::make('username')
                    ->label(__('green::admin-auth.admin-user.username'))
                    ->requiredWithout('email')
                    ->required(Plugin::get()->isEmailDisabled())
                    ->ascii()->alphaDash()
                    ->unique('admin_users', 'username', fn(?AdminUser $record) => $record)
                    ->hidden(Plugin::get()->isUsernameDisabled()),

                // パスワード
                \Green\AdminAuth\Forms\Components\PasswordForm::make()
                    ->visibleOn('create'),

                // グループ
                Forms\Components\Select::make('groups')
                    ->label(Plugin::get()->getGroupModelLabel())
                    ->relationship('groups', 'name')
                    ->options(self::getGroupOptions(true))
                    ->multiple(Plugin::get()->isMultipleGroups())
                    ->allowHtml()->native(false)->placeholder('')
                    ->requiredWithout('roles')
                    ->hidden(Plugin::get()->isGroupDisabled()),

                // ロール
                Forms\Components\Select::make('roles')
                    ->label(__('green::admin-auth.admin-user.roles'))
                    ->relationship('roles', 'name')
                    ->options(AdminRole::getOptions())
                    ->multiple(Plugin::get()->isMultipleRoles())
                    ->native(false)->placeholder('')
                    ->required(Plugin::get()->isGroupDisabled())
                    ->visible(auth()->user()->hasPermission(\Green\AdminAuth\Permissions\EditAdminUserRole::class)),
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
                \Green\AdminAuth\Tables\Columns\AvatarColumn::make('name')
                    ->label(__('green::admin-auth.admin-user.name'))
                    ->avatar(fn($record) => $record->avatar_url)
                    ->sortable()->searchable()->toggleable(),

                // メール
                Tables\Columns\TextColumn::make('email')
                    ->label(__('green::admin-auth.admin-user.email'))
                    ->sortable()->searchable()->toggleable()
                    ->hidden(fn() => Plugin::get()->isEmailDisabled()),

                // ユーザー名
                Tables\Columns\TextColumn::make('username')
                    ->label(__('green::admin-auth.admin-user.username'))
                    ->sortable()->searchable()->toggleable()
                    ->hidden(fn() => Plugin::get()->isUsernameDisabled()),

                // 状態
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('green::admin-auth.admin-user.is-active'))
                    ->boolean()
                    ->sortable()->toggleable(),

                // 管理グループ
                Tables\Columns\TextColumn::make('groups.name')
                    ->label(Plugin::get()->getGroupModelLabel())
                    ->badge()
                    ->sortable()->toggleable()
                    ->hidden(Plugin::get()->isGroupDisabled()),

                // 管理ロール
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('green::admin-auth.admin-user.roles'))
                    ->badge()
                    ->sortable()->toggleable(),

                // 最終ログイン
                Tables\Columns\TextColumn::make('login_logs_max_created_at')
                    ->label(__('green::admin-auth.admin-user.last-login-at'))
                    ->max('loginLogs', 'created_at')
                    ->since()
                    ->sortable()->toggleable(),

                // 作成日時
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('green::admin-auth.admin-user.created-at'))
                    ->sortable()->toggleable()->toggledHiddenByDefault(),

                // 更新日時
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('green::admin-auth.admin-user.updated-at'))
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

                    // パスワードをリセット
                    AdminUserResource\Actions\ResetPasswordAction::make()
                        ->modalWidth('sm')
                        ->visible(fn($record) => auth()->user()->can('resetPassword', $record)),

                    // ログインを停止
                    AdminUserResource\Actions\SuspendAction::make()
                        ->visible(fn($record) => $record->is_active && auth()->user()->can('suspend', $record)),

                    // ログインを再開
                    AdminUserResource\Actions\ResumeAction::make()
                        ->visible(fn($record) => !$record->is_active && auth()->user()->can('suspend', $record)),
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
     * @param bool $html
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
