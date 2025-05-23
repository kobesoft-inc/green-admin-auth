<?php

namespace Green\AdminAuth\Models\Concerns;

use Illuminate\Support\Str;
use RuntimeException;

trait GuessesRelated
{
    /**
     * 自分のクラスの名前が特定のキーワードで終わるかを取得する
     */
    private static function classNameEndsWith(string $suffix): bool
    {
        return str_ends_with(class_basename(static::class), $suffix);
    }

    /**
     * クラス名のサフィックスを置換して関連クラスを推測する
     */
    protected static function guessRelatedClass(string $fromSuffix, string $toSuffix): string
    {
        if (!self::classNameEndsWith($fromSuffix)) {
            throw new RuntimeException("Cannot guess class for [" . static::class . "]: does not end with '$fromSuffix'");
        }

        $base = Str::replaceLast($fromSuffix, $toSuffix, class_basename(static::class));
        $namespace = Str::beforeLast(static::class, '\\');
        $guessed = $namespace . '\\' . $base;

        if (!class_exists($guessed)) {
            throw new RuntimeException("Guessed class [$guessed] does not exist");
        }

        return $guessed;
    }

    /**
     * 指定のターゲットサフィックスに対応するクラスを取得
     */
    protected static function relatedClass(string $target): string
    {
        $suffixes = ['User', 'Group', 'Role'];
        foreach ($suffixes as $suffix) {
            if (self::classNameEndsWith($suffix)) {
                return $suffix === $target
                    ? static::class
                    : self::guessRelatedClass($suffix, $target);
            }
        }
        throw new RuntimeException("Cannot determine related class for [" . static::class . "] -> $target");
    }

    /**
     * ユーザークラスを取得
     */
    protected static function userClass(): string
    {
        return self::relatedClass('User');
    }

    /**
     * グループクラスを取得
     */
    protected static function groupClass(): string
    {
        return self::relatedClass('Group');
    }

    /**
     * ロールクラスを取得
     */
    protected static function roleClass(): string
    {
        return self::relatedClass('Role');
    }

    /**
     * ログイン履歴クラスを取得
     */
    protected static function loginLogClass(): string
    {
        return self::relatedClass('LoginLog');
    }

    /**
     * ユーザーが所属するグループのピボットテーブル名を取得する
     */
    protected static function userGroupPivotTable(): string
    {
        return Str::snake(class_basename(static::userClass())) . '_group';
    }

    /**
     * ユーザーが所属するロールのピボットテーブル名を取得する
     */
    protected static function userRolePivotTable(): string
    {
        return Str::snake(class_basename(static::userClass())) . '_role';
    }

    /**
     * グループが持つロールのピボットテーブル名を取得する
     */
    protected static function groupRolePivotTable(): string
    {
        return Str::snake(class_basename(static::groupClass())) . '_role';
    }

    /**
     * ユーザーのクラスへの外部キーの名前を取得する
     */
    protected static function userForeignKey(): string
    {
        return Str::snake(class_basename(static::userClass())) . '_id';
    }

    /**
     * グループのクラスへの外部キーの名前を取得する
     */
    protected static function groupForeignKey(): string
    {
        return Str::snake(class_basename(static::groupClass())) . '_id';
    }

    /**
     * ロールのクラスへの外部キーの名前を取得する
     */
    protected static function roleForeignKey(): string
    {
        return Str::snake(class_basename(static::roleClass())) . '_id';
    }
}
