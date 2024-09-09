<?php

namespace App\Modules\Branding;

use Cache;
use OzdemirBurak\Iris\Color\Rgb;

class Hex
{
    public Rgb $rgb;

    private int $red = 0;

    private int $green = 0;

    private int $blue = 0;

    public function __construct($hex)
    {
        $this->rgb = (new \OzdemirBurak\Iris\Color\Hex($hex))->toRgb();
        $this->red = (int) $this->rgb->red();
        $this->green = (int) $this->rgb->green();
        $this->blue = (int) $this->rgb->blue();
    }

    public static function isDark($hex): bool
    {
        return (new static($hex))->luma() <= 0.5;
    }

    public function luma()
    {
        return Cache::rememberForever('luma-for-'.$this->rgb->toHex(), function () {
            return ((0.2126 * $this->red) + (0.7152 * $this->green) + (0.0722 * $this->blue)) / 255;
        });
    }

    public static function isLight($hex): bool
    {
        return (new static($hex))->luma() > 0.5;
    }

}
