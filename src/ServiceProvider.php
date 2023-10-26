<?php

namespace Green\AdminBase;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * 登録処理
     *
     * @return void
     */
    public function register(): void
    {
    }

    /**
     * 初期起動処理
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'green');
        $this->loadViewsFrom(__DIR__.'/../resources', 'green');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}