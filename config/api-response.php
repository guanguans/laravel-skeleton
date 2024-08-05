<?php

/** @noinspection LaravelFunctionsInspection */

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Response;

return [
    /**
     * @see \App\Support\ApiResponse\ApiResponseServiceProvider::packageBooted()
     */
    'register_render_using' => env('API_RESPONSE_REGISTER_RENDER_USING', true),

    /**
     * @var callable(\Throwable $throwable, \Illuminate\Http\Request $request): ?\Illuminate\Http\JsonResponse $renderUsing
     */
    'render_using' => App\Support\ApiResponse\RenderUsing::class,

    /**
     * @see \App\Support\ApiResponse\ApiResponse::prependExceptionMap()
     * @see \App\Support\ApiResponse\ApiResponse::parseExceptionMap()
     */
    'exception_map' => [
        Illuminate\Auth\AuthenticationException::class => [
            'code' => Response::HTTP_UNAUTHORIZED,
        ],
    ],

    'pipes' => [
        App\Support\ApiResponse\Pipes\ResourceCollectionDataPipe::class,
        App\Support\ApiResponse\Pipes\JsonResourceDataPipe::class,
        App\Support\ApiResponse\Pipes\PaginatorDataPipe::class,
        App\Support\ApiResponse\Pipes\DefaultDataPipe::class,
        App\Support\ApiResponse\Pipes\MessagePipe::class,
        App\Support\ApiResponse\Pipes\ErrorPipe::class,
        App\Support\ApiResponse\Pipes\SetStatusCodePipe::with(200),
    ],
];
