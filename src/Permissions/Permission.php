<?php

namespace Green\AdminAuth\Permissions;

abstract class Permission
{
    /**
     * パーミッションの識別子
     *
     * @return string
     */
    static public function getId(): string
    {
        return static::class;
    }

    /**
     * パーミッションのグループ名
     *
     * @return string
     */
    abstract static public function getGroup(): string;

    /**
     * パーミッションの表示名
     *
     * @return string
     */
    abstract static public function getLabel(): string;

    /**
     * 起動時の処理
     *
     * @return void
     */
    static public function boot(): void
    {
        //
    }
}