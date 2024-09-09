<?php

namespace App\Modules\Dates;

use Carbon\Carbon;
use Carbon\CarbonImmutable;

use function foo\func;

class DateFormat
{
    const SHORT_DAY_NAME_DAY_MONTH = 1;

    private $date;

    private $format;

    public function __construct(Carbon|CarbonImmutable $date, $format)
    {
        $this->date = $date;

        if ($format) {
            $this->format = $format;
        } elseif ($user = auth()->user()) {
            $this->format = $user->date_format;
        }
    }

    public static function get_database_values_with_example()
    {
        return static::get_database_values()->map(function ($string) {
            return str_replace('YYYY', date('Y'), $string);
        })->map(function ($string) {
            return str_replace('DD', 28, $string);
        })->map(function ($string) {
            return str_replace('MM', 11, $string);
        });
    }

    public static function get_database_values()
    {
        return collect([
            'little-endian|dash', 'little-endian|slash', 'little-endian|dot', 'little-endian|space',
            'big-endian|dash', 'big-endian|slash', 'big-endian|dot', 'big-endian|space',
            'middle-endian|dash', 'middle-endian|slash', 'middle-endian|dot', 'middle-endian|space',
        ])->keyBy(function ($item) {
            return $item;
        })->map(function ($item) {
            $exploded = explode('|', $item);
            $form = $exploded[0];
            $s = static::render_separator($item);

            switch ($form) {
                case 'middle-endian':
                    return "MM{$s}DD{$s}YYYY";
                    break;
                case 'big-endian':
                    return "YYYY{$s}MM{$s}DD";
                    break;
                case 'little-endian':
                default:
                    return "DD{$s}MM{$s}YYYY";
                    break;
            }
        });
    }

    public static function render_separator($format)
    {
        $exploded = explode('|', $format);
        if (count($exploded) < 2 || $exploded[1] == 'dash') {
            return '-';
        }
        if ($exploded[1] == 'slash') {
            return '/';
        }
        if ($exploded[1] == 'dot') {
            return '.';
        }
        if ($exploded[1] == 'space') {
            return ' ';
        }

        return '-';
    }

    public function get_dmy()
    {
        $s = $this->get_separator();

        switch ($this->get_endian()) {
            case 'big-endian':
                return $this->date->formatLocalized("%Y{$s}%m{$s}%d");
                break;
            case 'middle-endian':
                return $this->date->formatLocalized("%m{$s}%d{$s}%Y");
                break;
            case 'little-endian':
            default:
                return $this->date->formatLocalized("%d{$s}%m{$s}%Y");
        }
    }

    private function get_separator()
    {
        return static::render_separator($this->format);
    }

    private function get_endian()
    {
        $exploded = explode('|', $this->format);

        if (!count($exploded)) {
            return 'little-endian';
        }

        return $exploded[0];
    }

    public function get_full_month_and_year()
    {
        return $this->date->formatLocalized('%B %Y');
    }

    public function get_hidmy()
    {
        $s = $this->get_separator();

        switch ($this->get_endian()) {
            case 'big-endian':
                return $this->date->formatLocalized("%H:%M %Y{$s}%m{$s}%d");
                break;
            case 'middle-endian':
                return $this->date->formatLocalized("%H:%M %m{$s}%d{$s}%Y");
                break;
            case 'little-endian':
            default:
                return $this->date->formatLocalized("%H:%M %d{$s}%m{$s}%Y");
        }
    }

    public function get_long_day_with_date_and_year()
    {
        switch ($this->get_endian()) {
            case 'big-endian':
            case 'middle-endian':
                return $this->date->formatLocalized('%A %B %e, %Y');
            case 'little-endian':
            default:
                return $this->date->formatLocalized('%A %e %B %Y');
        }
    }

    public function get_short_day_month()
    {
        // for example: 05/03
        $s = $this->get_separator();

        switch ($this->get_endian()) {
            case 'big-endian':
            case 'middle-endian':
                return $this->date->formatLocalized("%m{$s}%d");
                break;
            case 'little-endian':
            default:
                return $this->date->formatLocalized("%d{$s}%m");
        }
    }

    public function get_short_day_with_date()
    {
        // for example: mon. 05/03
        $s = $this->get_separator();

        switch ($this->get_endian()) {
            case 'big-endian':
            case 'middle-endian':
                return $this->date->formatLocalized("%a. %m{$s}%d");
                break;
            case 'little-endian':
            default:
                return $this->date->formatLocalized("%a. %d{$s}%m");
        }
    }
}
