<?php

namespace Green\AdminAuth\Models;

use Green\AdminAuth\Models\Role\Concerns\HasGroups;
use Green\AdminAuth\Models\Role\Concerns\HasUsers;
use Green\Support\Concerns\HasGetOptions;
use Green\Support\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Model;

/**
 * 管理ロール
 *
 * @property string $name
 * @property \Illuminate\Database\Eloquent\Collection $users
 * @property \Illuminate\Database\Eloquent\Collection $groups
 * @property \Illuminate\Support\Collection $permissions
 */
class AdminRole extends Model
{
    use HasSortOrder;
    use HasGetOptions;
    use HasUsers;
    use HasGroups;

    /**
     * 一括代入できる属性
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'permissions',
    ];

    /**
     * キャストする属性
     *
     * @var array<string, string>
     */
    protected $casts = [
        'permissions' => 'json',
    ];

    /**
     * ロールの選択肢
     *
     * @return array
     */
    static public function getOptions(): array
    {
        return static::defaultOrder()->get()->pluck('name', 'id')->toArray();
    }
}
