<?php

namespace Green\AdminAuth\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NestedSet;
use Kalnoy\Nestedset\NodeTrait;

/**
 * NestedSetの親要素として適切か？
 *
 * @private Model|NodeTrait $record
 */
class NodeParent implements ValidationRule
{
    /**
     * インスタンスを初期化する
     *
     * @param Model|null $record 現在のレコード
     */
    public function __construct(private readonly Model|null $record)
    {
    }

    /**
     * 検証ルールを実行する
     *
     * @param string $attribute 検証する属性名
     * @param mixed $value 検証する値
     * @param Closure $fail 検証失敗時のコールバック
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->record) {
            $class = $this->record::class;
            $parent = $class::find($value);
            if ($this->record->isAncestorOf($parent) || $this->record->id == $parent->id) {
                $fail(__('green::admin-auth.validations.node-parent'));
            }
        }
    }
}
