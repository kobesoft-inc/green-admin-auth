<?php

namespace Green\AdminAuth\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Green\AdminAuth\GreenAdminAuthPlugin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * 管理ユーザー
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $username
 * @property string $password
 * @property Carbon $password_expires_at
 * @property bool $is_active
 * @property string $avatar
 * @property string $avatar_url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property \Illuminate\Database\Eloquent\Collection $groups
 * @property \Illuminate\Database\Eloquent\Collection $roles
 * @property \Illuminate\Database\Eloquent\Collection $loginLogs
 * @property \Illuminate\Support\Collection $permissions
 */
class AdminUser extends \Illuminate\Foundation\Auth\User implements FilamentUser
{
    use SoftDeletes;

    /**
     * 一括代入できる属性
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'password_expires_at',
        'is_active',
        'avatar',
    ];

    /**
     * 配列に追加にする属性
     *
     * @var string[]
     */
    protected $appends = [
        'avatar_url',
    ];

    /**
     * 配列に非表示にする属性
     *
     * @var string[]
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * キャストする属性
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'password_expires_at' => 'datetime'
    ];

    /**
     * 起動時の処理
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        // 保存時
        static::saving(function (AdminUser $adminUser) {
            if ($adminUser->isDirty('password') && !$adminUser->isDirty('password_expires_at')) {
                // パスワードを変更し、有効期限を設定していない場合には、自動的に有効期限を設定する
                $passwordDays = GreenAdminAuthPlugin::get()->getPasswordDays();
                $adminUser->password_expires_at = $passwordDays
                    ? now()->addDays($passwordDays) // 有効期限を設定
                    : null; // 有効期限無し
            }
        });

        // 削除時
        static::deleting(function (AdminUser $adminUser) {
            // グループとロールの関連を削除
            $adminUser->groups()->detach();
            $adminUser->roles()->detach();
        });
    }

    /**
     * 指定されたグループ以下に所属するユーザーだけのスコープ
     *
     * @param Builder $query
     * @param \Illuminate\Support\Collection $groups
     * @return void
     */
    public function scopeInGroups(Builder $query, \Illuminate\Support\Collection $groups): void
    {
        $query->whereHas('groups', function (Builder $query) use ($groups) {
            $query->whereIn('admin_groups.id', $groups->pluck('id'));
        });
    }

    /**
     * この管理ユーザーが所属するグループ
     *
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminGroup::class,
            'admin_user_group',
            'admin_user_id',
            'admin_group_id'
        );
    }

    /**
     * この管理ユーザーに割り当てられたロール
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminRole::class,
            'admin_user_role',
            'admin_user_id',
            'admin_role_id'
        );
    }

    /**
     * この管理ユーザーが所属するグループ・子グループ
     *
     * @return \Illuminate\Support\Collection
     */
    public function groupsWithDescendants(): \Illuminate\Support\Collection
    {
        $groups = collect();
        foreach ($this->groups()->with('descendants')->get() as $group) {
            $groups = $groups->add($group)->concat($group->descendants);
        }
        return $groups->unique('id');
    }

    /**
     * ログイン履歴
     *
     * @return HasMany
     */
    public function loginLogs(): HasMany
    {
        return $this->hasMany(AdminLoginLog::class, 'admin_user_id');
    }

    /**
     * アバター画像のURL
     *
     * @return Attribute
     */
    public function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->avatar
                    ? Storage::disk('public')->url($this->avatar)
                    : app(filament()->getDefaultAvatarProvider())->get($this);
            }
        );
    }

    /**
     * パーミッション
     *
     * @return Attribute
     */
    public function permissions(): Attribute
    {
        return Attribute::make(
            get: function () {
                $permissions = collect();
                foreach ($this->roles as $role) {
                    $permissions = $permissions->concat($role->permissions);
                }
                foreach ($this->groups()->with('roles')->get() as $group) {
                    $permissions = $permissions->concat($group->permissions);
                }
                return $permissions->unique();
            }
        )->shouldCache();
    }

    /**
     * このユーザーが指定されたパーミッションを持っているか？
     *
     * @param string $permission パーミッションの識別子(通常はクラス名)
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions->contains($permission)
            || $this->permissions->contains(\Green\AdminAuth\Permissions\Super::class);
    }

    /**
     * パスワードが有効期限切れか？
     *
     * @return bool
     */
    public function isPasswordExpired(): bool
    {
        return $this->password_expires_at !== null
            && $this->password_expires_at->isPast();
    }

    /**
     * パネルにアクセスできるか？
     *
     * @param Panel $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
