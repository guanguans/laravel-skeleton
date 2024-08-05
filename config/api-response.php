<?php

/** @noinspection LaravelFunctionsInspection */

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Response;

return [
    'enabled' => env('API_RESPONSE_ENABLED', true),

    /**
     * @var callable(\Throwable $throwable, \Illuminate\Http\Request $request): ?\Illuminate\Http\JsonResponse $renderUsing
     */
    'render_using' => App\Support\ApiResponse\RenderUsing::class,

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
