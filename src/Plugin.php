<?php

namespace Green\AdminBase;

use Filament\Panel;

class Plugin implements \Filament\Contracts\Plugin
{
    /**
     * プラグインの識別子を返す
     *
     * @return string
     */
    public function getId(): string
    {
        return 'adminBase';
    }

    /**
     * 登録処理
     *
     * @param  Panel  $panel
     * @return void
     */
    public function register(Panel $panel): void
    {
    }

    /**
     * 初期起動処理
     *
     * @param  Panel  $panel
     * @return void
     */
    public function boot(Panel $panel): void
    {
    }

    /**
     * インスタンスを生成する
     *
     * @return static
     */
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * インスタンスを取得する
     *
     * @return static
     */
    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }
}