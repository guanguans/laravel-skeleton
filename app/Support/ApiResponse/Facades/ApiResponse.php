<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Support\ApiResponse\ApiResponse
 */
class ApiResponse extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Support\ApiResponse\ApiResponse::class;
    }
}
