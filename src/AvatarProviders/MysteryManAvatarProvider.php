<?php

namespace Green\AdminAuth\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Color\Rgb;

class MysteryManAvatarProvider implements AvatarProvider
{
    /**
     * アバター画像のURLを取得する(DATA URL形式)
     *
     * @param Model|Authenticatable $record
     * @return string
     */
    public function get(Model|Authenticatable $record): string
    {
        $background = Rgb::fromString('rgb(' . FilamentColor::getColors()['gray'][500] . ')')->toHex();
        $color = Rgb::fromString('rgb(' . FilamentColor::getColors()['gray'][100] . ')')->toHex();

        return "data:image/svg+xml," . rawurlencode(static::svg($background, $color));
    }

    /**
     * デフォルトのアバター画像を生成する(SVG形式)
     *
     * @param string $background
     * @param string $color
     * @return string
     */
    protected static function svg(string $background, string $color): string
    {
        return <<<SVG
<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg">
  <rect width="100" height="100" fill="$background" />
  <ellipse cx="50" cy="110" rx="50" ry="45" fill="$color" />
  <ellipse cx="50" cy="40" rx="24" ry="25" fill="$color" />
</svg>
SVG;
    }
}
