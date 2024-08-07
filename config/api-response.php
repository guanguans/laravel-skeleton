<?php

/** @noinspection LaravelFunctionsInspection */

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Response;

return [
    /**
     * @see \App\Support\ApiResponse\ApiResponseServiceProvider::registerRenderUsing()
     */
    'render_using_factory' => App\Support\ApiResponse\RenderUsingFactory::class,

    /**
     * @see \App\Support\ApiResponse\ApiResponse::mapException()
     */
    'exception_map' => [
        Illuminate\Auth\AuthenticationException::class => [
            'code' => Response::HTTP_UNAUTHORIZED,
        ],
        // Illuminate\Database\QueryException::class => [
        //     'message' => '',
        //     'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
        // ],
        // Illuminate\Validation\ValidationException::class => [
        //     'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
        // ],
        // Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class => [
        //     'message' => '',
        // ],
        // Illuminate\Database\Eloquent\ModelNotFoundException::class => [
        //     'message' => '',
        // ],
    ],

    'pipes' => [
        App\Support\ApiResponse\Pipes\DataPipe::class,
        App\Support\ApiResponse\Pipes\MessagePipe::class,
        App\Support\ApiResponse\Pipes\ErrorPipe::class,
        // App\Support\ApiResponse\Pipes\SetStatusCodePipe::class,
    ],
];
