<?php

/** @noinspection LaravelFunctionsInspection */

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Response;

return [
    /**
     * @see \App\Support\ApiResponse\ApiResponseServiceProvider::registerRenderUsing()
     */
    'render_using_creator' => App\Support\ApiResponse\RenderUsingCreator::class,

    /**
     * @see \App\Support\ApiResponse\ApiResponse::mapException()
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
        // App\Support\ApiResponse\Pipes\SetStatusCodePipe::class,
    ],
];
