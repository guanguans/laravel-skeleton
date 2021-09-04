<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCodeEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidRepeatRequestException extends HttpException
{
    public function __construct(string $message = 'Invalid repeat request.', int $statusCode =  HttpStatusCodeEnum::HTTP_FORBIDDEN)
    {
        parent::__construct($statusCode, $message);
    }
}
