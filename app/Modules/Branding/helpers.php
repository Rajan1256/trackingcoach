<?php

use App\Models\Team;
use App\Modules\Branding\Hex;
use App\Modules\Branding\Theme;
use Illuminate\Support\Arr;

if (!function_exists('theme')) {
    /**
     * @return Theme
     */
    function theme()
    {
        return new Theme();
    }
}

if (!function_exists('array_except')) {
    function array_except($array, $keys)
    {
        return Arr::except($array, $keys);
    }
}

if (!function_exists('current_team')) {
    function current_team(): ?Team
    {
        return Team::current();
    }
}

if (!function_exists('hex_is_dark')) {
    function hex_is_dark($hex)
    {
        return Hex::isDark($hex);
    }
}

if (!function_exists('hex_is_light')) {
    function hex_is_light($hex)
    {
        return Hex::isLight($hex);
    }
}
