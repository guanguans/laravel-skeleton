<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Jiannei\Response\Laravel\Support\Facades\Response;
use Jiannei\Response\Laravel\Support\Traits\ExceptionTrait;
use Throwable;

class Handler extends ExceptionHandler
{
    use ExceptionTrait{
        invalidJson as invalidValidationJson;
    }

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

    public function report(Throwable $e)
    {
        // 异常报错通知
        $this->shouldReport($e) and \ExceptionNotifier::report($e);

        parent::report($e);
    }

    public function render($request, Throwable $e)
    {
        // if ($request->is('api/*')) {
        //     return $this->prepareJsonResponse($request, $e);
        // }

        return parent::render($request, $e);
    }

    /**
     * Custom Failed Validation Response for Laravel.
     *
     * @param  Request  $request
     * @param  ValidationException  $exception
     * @return JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return Response::fail(
            $exception->validator->errors()->first(),
            Arr::get(Config::get('response.exception'), ValidationException::class.'.code', 422),
            $exception->errors()
        );
    }
}
