<?php

namespace App\Modules\Team\Exceptions;

use Exception;

class NoCurrentTeamException extends Exception
{
    public static function make()
    {
        return new static('The request expected a current team but none was set.');
    }
}
