<?php

namespace Green\AdminAuth\Tables\Columns;

use Filament\Tables\Columns\Column;

/**
 * アバター画像を表示するカラム
 *
 * @package Green\AdminAuth\Tables\Columns
 */
class AvatarColumn extends Column
{
    protected string $view = 'green::tables.columns.avatar-column';
    protected string|\Closure|null $avatar = null;

    /**
     * アバター画像を取得する
     *
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->evaluate($this->avatar);
    }

    /**
     * アバター画像を設定する
     *
     * @param string|\Closure|null $avatar
     * @return AvatarColumn
     */
    public function avatar(string|\Closure|null $avatar): AvatarColumn
    {
        $this->avatar = $avatar;
        return $this;
    }

}
