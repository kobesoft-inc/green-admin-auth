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
        $primaryKey = (new static())->primaryKey;
        $query = static::query();
        if ($closure) {
            $query = $closure($query);
        }
        return $query
            ->orderBy(self::SORT_ORDER)
            ->pluck(static::TITLE, $primaryKey)
            ->toArray();
    }
}