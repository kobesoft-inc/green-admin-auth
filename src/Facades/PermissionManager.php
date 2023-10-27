<?php

namespace Green\AdminBase\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * パーミッションの管理ファサード
 *
 * @method static register(string[] $array): void
 * @method static getGroups(): Collection
 * @method static getOptions(string $group): array
 */
class PermissionManager extends Facade
{
    /**
     * コンポーネントの登録名を取得
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \Green\AdminBase\Permissions\PermissionManager::class;
    }
}