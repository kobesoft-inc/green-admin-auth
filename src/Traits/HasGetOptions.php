<?php

namespace Green\AdminBase\Traits;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait HasGetOptions
{
    /**
     * 並び順のカラム
     */
    const SORT_ORDER = 'sort_order';

    /**
     * 選択肢のラベルのカラム
     */
    const TITLE = 'name';

    /**
     *  このモデルの選択肢を取得する
     *
     * @param  Closure|null  $closure  条件を加えるためのクロージャ
     * @return array<string, string>
     */
    static public function getOptions(Closure $closure = null): array
    {
        // IDのカラム
        $idColumn = (new static())->primaryKey;

        // 選択肢のタイトルのカラム
        $titleColumn = defined(static::class.'::TITLE') ? static::TITLE : 'name';

        // 並び順のカラム
        $orderColumn = defined(static::class.'::SORT_ORDER') ? static::SORT_ORDER : $idColumn;

        // 選択肢を取得
        $query = static::query();
        if ($closure) {
            $query = $closure($query);
        }
        return $query
            ->orderBy($orderColumn)
            ->pluck($titleColumn, $idColumn)
            ->toArray();
    }
}