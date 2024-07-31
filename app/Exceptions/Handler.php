<?php

namespace App\Exceptions;

use App\Support\Api\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Jiannei\Response\Laravel\Support\Traits\ExceptionTrait;
use Throwable;

class Handler extends ExceptionHandler
{
    // use ExceptionTrait;

    /**
     * {@inheritdoc}
     */
    protected $dontReport = [
        //
    ];

    /**
     * {@inheritdoc}
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(static function (Throwable $e): void {});
        $this->renderable(ApiResponse::renderUsing());
    }

    public function report(Throwable $e): void
    {
        parent::report($e);
    }

    public function render($request, Throwable $e)
    {
        return parent::render($request, $e);
    }
}
