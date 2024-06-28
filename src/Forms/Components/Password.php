<?php

namespace Green\AdminAuth\Forms\Components;

use Closure;
use Green\AdminAuth\GreenAdminAuthPlugin;
use Illuminate\Support\Str;

class Password extends \Phpsa\FilamentPasswordReveal\Password
{
    static protected string $symbols = '!#$%&()*+,-./:;<=>?@[\]^_`{|}~';

    /**
     * パスワードコンポーネントの生成
     *
     * @param string $name
     * @return static
     */
    public static function make(string $name): static
    {
        return parent::make($name)
            ->ascii()
            ->minLength(GreenAdminAuthPlugin::get()->getPasswordMinLength())
            ->passwordUsesNumbers(GreenAdminAuthPlugin::get()->getPasswordMustUseNumbers())
            ->passwordUsesSymbols(GreenAdminAuthPlugin::get()->getPasswordMustUseSymbols())
            ->rule(fn() => function (string $attribute, mixed $value, Closure $fail) {
                if (GreenAdminAuthPlugin::get()->getPasswordMustUseNumbers() && !preg_match('/[0-9]/', $value)) {
                    $fail(__('green::admin-auth.validations.password-must-use-numbers'));
                }
                if (GreenAdminAuthPlugin::get()->getPasswordMustUseSymbols() && Str::contains($value, str_split(static::$symbols, 1)) === false) {
                    $fail(__('green::admin-auth.validations.password-must-use-symbols'));
                }
            });
    }
}
