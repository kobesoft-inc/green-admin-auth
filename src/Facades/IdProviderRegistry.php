<?php

namespace Green\AdminAuth\Facades;

use Green\AdminAuth\IdProviders\IdProvider;
use Illuminate\Support\Facades\Facade;

/**
 * IdPの管理ファサード
 *
 * @method static all(?string $guard = null): array
 * @method static get(string $driver, ?string $guard = null): ?\Green\AdminAuth\IdProviders\IdProvider
 * @method static register(IdProvider $idProvider, ?string $guard = null): void
 */
class IdProviderRegistry extends Facade
{
    /**
     * コンポーネントの登録名を取得
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \Green\AdminAuth\Services\IdProviderRegistry::class;
    }
}
