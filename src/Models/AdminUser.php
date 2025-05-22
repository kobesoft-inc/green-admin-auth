<?php

namespace Green\AdminAuth\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
class AdminUser extends \Illuminate\Foundation\Auth\User implements
    FilamentUser,
    User\Contracts\ShouldExpirePassword,
    User\Contracts\ShouldBelongsToGroups,
    User\Contracts\ShouldHaveRoles,
    User\Contracts\ShouldHaveUsername,
    User\Contracts\ShouldHaveAvatar,
    User\Contracts\CanBeSuspended,
    User\Contracts\ShouldHavePermissions
{
    use SoftDeletes;
    use User\Concerns\HasPasswordExpiration;
    use User\Concerns\BelongsToGroups;
    use User\Concerns\HasRoles;
    use User\Concerns\HasUsername;
    use User\Concerns\HasAvatar;
    use User\Concerns\HasSuspension;
    use User\Concerns\HasPermissions;

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
     * ログイン履歴
     *
     * @return HasMany
     */
    public function loginLogs(): HasMany
    {
        return $this->hasMany(AdminLoginLog::class, 'admin_user_id');
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
