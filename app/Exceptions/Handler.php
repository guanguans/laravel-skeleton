<?php

namespace App\Exceptions;

use Guanguans\LaravelExceptionNotify\Facades\ExceptionNotifier;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Jiannei\Response\Laravel\Support\Traits\ExceptionTrait;
use Throwable;

class Handler extends ExceptionHandler
{
    use ExceptionTrait;

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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // 异常报错通知
            ExceptionNotifier::reportIf($this->shouldReport($e), $e);
        });
    }

    public function report(Throwable $e)
    {
        parent::report($e);
    }

    public function render($request, Throwable $e)
    {
        return parent::render($request, $e);
    }
}
