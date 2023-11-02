<?php

namespace Green\AdminBase\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * 並び順カラムがある
 */
trait HasSortOrder
{
    const SORT_ORDER = 'sort_order';

    /**
     * 起動時の処理
     *
     * @return void
     */
    static public function bootHasSortOrder(): void
    {
        // 作成時にソート順をIDで初期化する
        self::created(function (Model $model) {
            $model->update([self::SORT_ORDER => $model->id]);
        });
    }

    /**
     * デフォルトの並び順のスコープ
     */
    public function scopeDefaultOrder(Builder $builder): Builder
    {
        return $builder->orderBy(self::SORT_ORDER);
    }
}