<?php

namespace Green\AdminAuth\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * 並び順のカラムがある
 */
trait HasSortOrder
{
    /**
     * 起動時の処理
     *
     * @return void
     */
    static public function bootHasSortOrder(): void
    {
        // 作成時にソート順をIDで初期化する
        self::created(function (Model $model) {
            $model->update([static::getSortOrder() => $model->id]);
        });
    }

    /**
     * デフォルトの並び順のスコープ
     */
    public function scopeDefaultOrder(Builder $builder): Builder
    {
        return $builder->orderBy(static::getSortOrder());
    }

    /**
     * 並び順のカラムを取得する
     *
     * @return string
     */
    static public function getSortOrder(): string
    {
        return defined(static::class . '::SORT_ORDER') ? static::SORT_ORDER : 'sort_order';
    }
}
