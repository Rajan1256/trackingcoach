<?php

namespace App\Modules\Timezones;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;

class Timezone
{
    public function listAll()
    {
        $timezones = [];
        foreach (timezone_identifiers_list() as $tz) {
            $timezones[] = [
                'timezone' => $tz,
                'time' => $this->convertFromLocal(Carbon::now(), $tz),
            ];
        }

        return $timezones;
    }

    public function listAllForSelect()
    {
        $timezones = [];

        return collect((array) timezone_identifiers_list())
            ->map(function ($name) {
                $tz = new DateTimeZone($name);
                $offset = $tz->getOffset(new DateTime);
                $offset_prefix = $offset < 0 ? '-' : '+';
                $offset_formatted = gmdate('H:i', abs($offset));

                $pretty_offset = "UTC${offset_prefix}${offset_formatted}";
                $exploded = explode('/', $name, 2);

                return [
                    'timezone' => $name,
                    'name' => count($exploded) > 1 ? $exploded[1] : $exploded[0],
                    'offset_seconds' => $offset,
                    'offset_string' => $pretty_offset,
                ];
            })
            ->sortBy(function ($item) {
                return $item['offset_seconds'];
            })
            ->groupBy(function ($tz) {
                return explode('/', $tz['timezone'], 2)[0];
            })->sortBy(function ($data, $key) {
                return $key;
            });
    }

    public function convertFromLocal($date, $timezone, $format = 'Y-m-d H:i:s')
    {
        if (! $date instanceof Carbon) {
            $date = new Carbon($date);
        }

        $date->setTimezone(new DateTimeZone($timezone));

        return $date->format($format);
    }

    public function convertToLocal($date, $timezone, $format = 'Y-m-d H:i:s')
    {
        $date = new Carbon($date, new DateTimeZone($timezone));

        $date->setTimezone(config('app.timezone'));

        return $date->format($format);
    }
}
