<?php

namespace Green\AdminAuth\Models;

use Green\Support\Concerns\HasNodeOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Kalnoy\Nestedset\NodeTrait;

/**
 * 管理グループ
 *
 * @property string $name
 * @property \Illuminate\Database\Eloquent\Collection $users
 * @property \Illuminate\Database\Eloquent\Collection $roles
 * @property \Illuminate\Support\Collection $permissions
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class AdminGroup extends Model implements
    Group\Contracts\ShouldHaveUsers,
    Group\Contracts\ShouldHaveRoles
{
    use NodeTrait, HasNodeOptions;
    use Group\Concerns\HasUsers;
    use Group\Concerns\HasRoles;

    /**
     * 一括代入できる属性
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'parent_id',
    ];
}
