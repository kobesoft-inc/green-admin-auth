<?php

namespace Green\AdminAuth\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Green\AdminAuth\Filament\Resources\AdminUserResource\Pages\ManageAdminUsers;
use Green\AdminAuth\Models\AdminGroup;
use Green\AdminAuth\Models\AdminRole;
use Green\AdminAuth\Models\AdminUser;
use Green\AdminAuth\Permissions\ManageAdminUser;
use Green\AdminAuth\Permissions\ManageAdminUserInGroup;
use Green\AdminAuth\GreenAdminAuthPlugin;
use Green\ResourceModule\Facades\ModuleRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Unique;

/**
 * 管理ユーザーのリソース
 */
class AdminUserResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicons-s-user';
    protected static ?int $navigationSort = 1100;

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
        return GreenAdminAuthPlugin::get()->getUserModel();
    }

    /**
     * モデルの名前
     *
     * @return string
     */
    public static function getModelLabel(): string
    {
        return GreenAdminAuthPlugin::get()->getUserModelLabel();
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
                // アバター
                Forms\Components\FileUpload::make('avatar')
                    ->label(__('green::admin-auth.admin-user.avatar'))
                    ->hiddenLabel()
                    ->avatar()
                    ->alignCenter()
                    ->hidden(GreenAdminAuthPlugin::get()->isAvatarDisabled()),

                // 名前
                Forms\Components\TextInput::make('name')
                    ->label(__('green::admin-auth.admin-user.name'))
                    ->required()->maxLength(20),

                // メール
                Forms\Components\TextInput::make('email')
                    ->label(__('green::admin-auth.admin-user.email'))
                    ->required(GreenAdminAuthPlugin::get()->isUsernameDisabled())
                    ->email()->maxLength(100)
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn(Unique $rule) => $rule->whereNull('deleted_at'))
                    ->hidden(GreenAdminAuthPlugin::get()->isEmailDisabled()),

                // ユーザー名
                Forms\Components\TextInput::make('username')
                    ->label(__('green::admin-auth.admin-user.username'))
                    ->requiredWithout('email')
                    ->required(GreenAdminAuthPlugin::get()->isEmailDisabled())
                    ->ascii()->alphaDash()
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn(Unique $rule) => $rule->whereNull('deleted_at'))
                    ->hidden(GreenAdminAuthPlugin::get()->isUsernameDisabled()),

                // パスワード
                \Green\AdminAuth\Forms\Components\PasswordForm::make()
                    ->visibleOn('create'),

                // グループ
                Forms\Components\Select::make('groups')
                    ->label(GreenAdminAuthPlugin::get()->getGroupModelLabel())
                    ->relationship('groups', 'name')
                    ->options(self::getGroupOptions(true))
                    ->multiple(GreenAdminAuthPlugin::get()->isMultipleGroups())
                    ->allowHtml()->native(false)->placeholder('')
                    ->required(fn(Get $get) => !filled($get('roles')))
                    ->hidden(GreenAdminAuthPlugin::get()->isGroupDisabled()),

                // ロール
                Forms\Components\Select::make('roles')
                    ->label(__('green::admin-auth.admin-user.roles'))
                    ->relationship('roles', 'name')
                    ->options(AdminRole::getOptions())
                    ->multiple(GreenAdminAuthPlugin::get()->isMultipleRoles())
                    ->native(false)->placeholder('')
                    ->required(GreenAdminAuthPlugin::get()->isGroupDisabled())
                    ->visible(auth()->user()->hasPermission(\Green\AdminAuth\Permissions\EditAdminUserRole::class)),
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
                \Green\AdminAuth\Tables\Columns\AvatarColumn::make('name')
                    ->label(__('green::admin-auth.admin-user.name'))
                    ->avatar(fn($record) => $record->avatar_url)
                    ->sortable()->searchable()->toggleable(),

                // メール
                Tables\Columns\TextColumn::make('email')
                    ->label(__('green::admin-auth.admin-user.email'))
                    ->sortable()->searchable()->toggleable()
                    ->hidden(fn() => GreenAdminAuthPlugin::get()->isEmailDisabled()),

                // ユーザー名
                Tables\Columns\TextColumn::make('username')
                    ->label(__('green::admin-auth.admin-user.username'))
                    ->sortable()->searchable()->toggleable()
                    ->hidden(fn() => GreenAdminAuthPlugin::get()->isUsernameDisabled()),

                // 状態
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('green::admin-auth.admin-user.is-active'))
                    ->boolean()
                    ->sortable()->toggleable(),

                // 管理グループ
                Tables\Columns\TextColumn::make('groups.name')
                    ->label(GreenAdminAuthPlugin::get()->getGroupModelLabel())
                    ->badge()
                    ->sortable()->toggleable()
                    ->hidden(GreenAdminAuthPlugin::get()->isGroupDisabled()),

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
                // 部署でフィルタ
                Tables\Filters\SelectFilter::make('groups')
                    ->label(GreenAdminAuthPlugin::get()->getGroupModelLabel())
                    ->options(self::getGroupOptions(true))
                    ->native(false)
                    ->hidden(GreenAdminAuthPlugin::get()->isGroupDisabled())
                    ->query(function ($query, $data) {
                        $query->when($data['value'], function ($query, $value) {
                            $query->whereHas('groups', function ($query) use ($value) {
                                $query->where('admin_groups.id', Arr::wrap($value));
                            });
                        });
                    }),

                // ロールでフィルタ
                Tables\Filters\SelectFilter::make('roles')
                    ->label(__('green::admin-auth.admin-user.roles'))
                    ->options(AdminRole::getOptions())
                    ->native(false)
                    ->query(function ($query, $data) {
                        $query->when($data['value'], function ($query, $value) {
                            $query->whereHas('roles', function ($query) use ($value) {
                                $query->where('admin_roles.id', Arr::wrap($value));
                            });
                        });
                    }),
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
            'index' => ManageAdminUsers::route('/'),
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
            return collect(AdminGroup::getOptions(null, $html))
                ->intersectByKeys(auth()->user()->groupsWithDescendants()->pluck('id')->flip())
                ->toArray();
        } else {
            return AdminGroup::getOptions(null, $html);
        }
    }
}
