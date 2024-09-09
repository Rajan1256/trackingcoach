<?php

if (! function_exists('timezone_helper')) {

    /**
     * @return \App\Modules\Timezones\Timezone
     */
    function timezone_helper()
    {
        return new \App\Modules\Timezones\Timezone();
    }
}
