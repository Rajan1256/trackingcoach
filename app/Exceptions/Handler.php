<?php

namespace App\Exceptions;

use App\Http\Kernel;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Route;
use Throwable;

use function config;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        // The first try fix is something that's hacky, but needed
        if ($this->isHttpException($e) && !$request->get('first_try', true)) {
            Route::any(request()->path(), function () use ($e, $request) {
                return parent::render($request, $e);
            })->middleware(config('pretty-error-pages.middleware'));
            $request->merge(['first_try' => false]);
            return app()->make(Kernel::class)->handle($request);
        } else {
            return parent::render($request, $e);
        }
    }
}
