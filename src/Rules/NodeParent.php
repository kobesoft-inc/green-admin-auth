<?php

namespace Green\AdminAuth\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;

/**
 * NestedSetの親要素として適切か？
 */
class NodeParent implements ValidationRule
{
    /**
     * 現在のレコード
     *
     * @var Model|null
     */
    private ?Model $record;

    /**
     * インスタンスを初期化する
     *
     * @param  Model|null  $record  現在のレコード
     */
    public function __construct(?Model $record)
    {
        $this->record = $record;
    }

    /**
     * 検証ルールを実行する
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