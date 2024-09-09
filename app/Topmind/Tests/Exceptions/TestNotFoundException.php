<?php

namespace App\Topmind\Tests\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TestNotFoundException extends HttpException
{
    public function __construct($message = null, Exception $previous = null, $code = 0)
    {
        parent::__construct(404, $message, $previous, [], $code);
    }
}
