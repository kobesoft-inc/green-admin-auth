<?php

namespace Green\AdminAuth\Services;

use Green\AdminAuth\IdProviders\IdProvider;

class IdProviderRegistry
{
    /**
     * コンストラクタ
     *
     * @param array $idProviders IdPの配列
     */
    public function __construct(
        protected array $idProviders = []
    )
    {
    }

    /**
     * IdPを取得する
     *
     * @param string|null $guard ガード名
     * @return array IdPの配列
     */
    public function all(?string $guard = null): array
    {
        return collect($this->idProviders)
            ->filter(fn($entry) => $entry['guard'] === $guard)
            ->map(fn($entry) => $entry['provider'])
            ->values()
            ->all();
    }

    /**
     * 特定のIdPを取得する
     *
     * @param string $driver IdPのドライバー名
     * @param string|null $guard ガード名
     * @return IdProvider|null IdPのインスタンス
     */
    public function get(string $driver, ?string $guard = null): ?IdProvider
    {
        return collect($this->all($guard))
            ->first(fn($provider) => $provider->getDriver() === $driver) ?? null;
    }

    /**
     * IdPを追加する
     *
     * @param IdProvider $idProvider IdPのインスタンス
     */
    public function register(IdProvider $idProvider, ?string $guard = null): void
    {
        $this->idProviders[] = [
            'provider' => $idProvider,
            'guard' => $guard,
        ];
    }
}
