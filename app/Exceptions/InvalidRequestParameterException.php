<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCodeEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidRequestParameterException extends HttpException
{
    public function __construct(string $message = 'Invalid request parameters.', int $statusCode =  HttpStatusCodeEnum::HTTP_BAD_REQUEST)
    {
        parent::__construct($statusCode, $message);
    }
}
