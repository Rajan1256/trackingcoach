<?php

use App\Models\Team;

if (!function_exists('to_real_float')) {
    function to_real_float($number): string
    {
        $number = (float) $number;
        $string = strval($number);

        return str_replace(',', '.', $string);
    }
}

if (!function_exists('one_decimal')) {
    function one_decimal($number)
    {
        if (!$number) {
            return $number;
        }

        if (!is_numeric($number)) {
            return $number;
        }

        return round($number * 10) / 10;
    }
}

if (!function_exists('yesNoVal')) {
    function yesNoVal($boolean)
    {
        if (is_null($boolean)) {
            return;
        }

        return boolval($boolean) ? 'yes' : 'no';
    }
}

if (!function_exists('scoreToColorClass')) {
    function scoreToColorClass($score)
    {
        $score = intval($score);
        if ($score < 60) {
            return 'red';
        } elseif ($score < 80) {
            return 'yellow';
        } else {
            return 'green';
        }
    }
}

if (!function_exists('replaceNames')) {
    function replaceNames($string, App\Models\User $customer = null)
    {
        if (!$customer) {
            return $string;
        }

        $string = str_replace('FIRST_NAME', $customer->first_name, $string);
        $string = str_replace('LAST_NAME', $customer->last_name, $string);
        $string = str_replace('NAME', $customer->name, $string);

        return $string;
    }
}

if (!function_exists('getClientIp')) {
    /**
     * @return null|string
     */
    function getClientIp()
    {
        foreach (
            [
                'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP',
                'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR',
            ] as $key
        ) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP,
                            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }
}

if (!function_exists('current_team')) {
    /**
     * @return Team|null
     */
    function current_team(): ?Team
    {
        return Team::current();
    }
}
