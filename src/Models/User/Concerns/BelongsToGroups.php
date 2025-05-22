<?php

namespace Green\AdminAuth\Models\User\Concerns;

use Green\AdminAuth\Models\Concerns\GuessesRelated;
use Green\AdminAuth\Models\User\Contracts\ShouldBelongsToGroups;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

/**
 * ユーザーはグループに所属する
 *
 * @mixin Model|GuessesRelated
 */
trait BelongsToGroups
{
    use GuessesRelated;

    /**
     * 起動時の処理
     *
     * @return void
     */
    public static function bootBelongsToGroups(): void
    {
        static::deleting(function (Model|ShouldBelongsToGroups $model) {
            $model->groups()->detach();
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
            static::groupClass(),
            static::userGroupPivotTable(),
            static::userForeignKey(),
            static::groupForeignKey()
        );
    }

    /**
     * 指定されたグループ以下に所属するユーザーだけのスコープ
     *
     * @param Builder $query
     * @param Collection $groups
     * @return void
     */
    public function scopeInGroups(Builder $query, Collection $groups): void
    {
        $query->whereHas('groups', function (Builder $query) use ($groups) {
            $query->whereIn($this->groupTable() . '.id', $groups->pluck('id'));
        });
    }

    /**
     * この管理ユーザーが所属するグループ・子グループ
     *
     * @return Collection
     */
    public function groupsWithDescendants(): Collection
    {
        $groups = collect();
        foreach ($this->groups()->with('descendants')->get() as $group) {
            $groups = $groups->add($group)->concat($group->descendants);
        }
        return $groups->unique('id');
    }
}
