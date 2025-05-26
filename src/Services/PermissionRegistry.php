<?php

namespace Green\AdminAuth\Services;

use Illuminate\Support\Collection;

class PermissionRegistry
{
    protected Collection $permissions;

    /**
     * インスタンスを初期化する
     */
    public function __construct()
    {
        $this->permissions = collect();
    }

    /**
     * パーミッションを登録する
     *
     * @param string[] $permissions パーミッションのクラス名の配列
     */
    public function register(array $permissions): void
    {
        foreach ($permissions as $permission) {
            $permission::boot();
            $this->permissions->add($permission);
        }
    }

    /**
     * 登録済みのパーミッションのグループを取得する
     *
     * @return Collection パーミッションのグループ
     */
    public function getGroups(): Collection
    {
        return $this->permissions
            ->map(fn(string $permission) => $permission::getGroup())
            ->unique();
    }

    /**
     * 登録済みのパーミッションを取得する
     *
     * @param string $group パーミッションのグループ
     * @return array 選択肢
     */
    public function getOptions(string $group): array
    {
        return $this->permissions
            ->filter(fn(string $permission) => $permission::getGroup() == $group)
            ->mapWithKeys(fn(string $permission) => [$permission::getId() => $permission::getLabel()])
            ->toArray();
    }
}
