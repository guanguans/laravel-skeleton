<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
    #[\Override]
    public function register(): void
    {
        $this->reportable(static function (Throwable $e): void {});
    }

    #[\Override]
    public function report(Throwable $e): void
    {
        parent::report($e);
    }

    #[\Override]
    public function render($request, Throwable $e)
    {
        return parent::render($request, $e);
    }
}
