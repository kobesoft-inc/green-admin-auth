<?php

namespace Green\AdminAuth\Models\User\Concerns;

use Green\AdminAuth\Models\User\Contracts\CanBeSuspended;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * ユーザーを停止できる
 *
 * @mixin Model
 */
trait HasSuspension
{
    /**
     * 起動時の処理
     */
    protected static function bootHasSuspension(): void
    {
        static::creating(function (CanBeSuspended $model) {
            // 利用停止・利用停止日時のカラムが定義されていない場合、アクティブな状態にする
            if ($model->suspendedAtColumn() && !isset($model->{$model->suspendedAtColumn()})) {
                $model->{$model->suspendedAtColumn()} = null;
            } elseif ($model->isActivateColumn() && !isset($model->{$model->isActivateColumn()})) {
                $model->{$model->isActivateColumn()} = true;
            }
        });
    }

    /**
     * ユーザーの利用停止をした日時のカラム名
     */
    public function suspendedAtColumn(): ?string
    {
        return 'suspended_at';
    }

    /**
     * ユーザーが利用できるか？のカラム名
     *
     * 互換性のために実装している
     */
    public function isActivateColumn(): ?string
    {
        return null;
    }

    /**
     * ユーザーを停止する
     */
    public function suspend(): void
    {
        if ($column = $this->suspendedAtColumn()) {
            $this->{$column} = now();
        } else if ($column = $this->isActivateColumn()) {
            $this->{$this->$column} = true;
        } else {
            throw new RuntimeException('is_active and suspended_at is not defined.');
        }
    }

    /**
     * ユーザーを再開する
     */
    public function resume(): void
    {
        if ($column = $this->suspendedAtColumn()) {
            $this->{$column} = null;
        } else if ($column = $this->isActivateColumn()) {
            $this->{$this->$column} = false;
        } else {
            throw new RuntimeException('is_active and suspended_at is not defined.');
        }
    }

    /**
     * 現在、アクティブか？の属性
     */
    public function isActive(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($column = $this->suspendedAtColumn()) {
                    return $this->{$column} === null;
                } else if ($column = $this->isActivateColumn()) {
                    return (bool)$this->{$column};
                } else {
                    return true;
                }
            }
        );
    }

    /**
     * 現在、利用停止中か？の属性
     */
    public function isSuspended(): Attribute
    {
        return Attribute::make(
            get: function () {
                return !$this->isActive;
            }
        );
    }
}
