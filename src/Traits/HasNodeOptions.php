<?php

namespace Green\AdminBase\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * NestedSetの選択肢
 */
trait HasNodeOptions
{
    /**
     * このモデルの選択肢を取得する
     *
     * @param  bool  $html  選択肢をHTML文字列で表現するか？
     * @return array<string, string>
     */
    static public function getOptions(bool $html = true): array
    {
        return static::defaultOrder()->get()
            ->mapWithKeys(fn(Model $model) => [$model->id => $html ? $model->getOptionLabel() : $model->name])
            ->toArray();
    }

    /**
     * 選択肢の文字列を取得する
     *
     * @return string  選択肢の文字列
     */
    private function getOptionLabel(): string
    {
        return
            ($this->parent_id
                ? '<span class="text-gray-500">'.e($this->ancestors->pluck('name')->join(' > ').' > ').'</span>'
                : ''
            ).'<span class="text-gray-950">'.e($this->name).'</span>';
    }
}