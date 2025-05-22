<?php

namespace Green\AdminAuth\Models\User\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * アバター画像を持つ
 *
 * @property string $avatar
 * @property string $avatar_url
 * @mixin Model
 */
trait HasAvatar
{
    /**
     * アバター画像のURL
     *
     * @return Attribute
     */
    public function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->avatar
                    ? Storage::disk('public')->url($this->avatar)
                    : app(filament()->getDefaultAvatarProvider())->get($this);
            }
        );
    }
}
