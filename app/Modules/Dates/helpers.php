<?php

use App\Modules\Dates\DateFormat;
use Carbon\CarbonImmutable;

if (!function_exists('date_helper')) {
    function date_format_helper(Carbon\Carbon|CarbonImmutable $date, $format = '')
    {
        return new DateFormat($date, $format);
    }
}
