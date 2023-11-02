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
        // デフォルトの並び順
        self::addGlobalScope('defaultOrder', function (Builder $builder) {
            $builder->orderBy(self::SORT_ORDER);
        });

        // 作成時にソート順をIDで初期化する
        self::created(function (Model $model) {
            $model->update([self::SORT_ORDER => $model->id]);
        });
    }
}