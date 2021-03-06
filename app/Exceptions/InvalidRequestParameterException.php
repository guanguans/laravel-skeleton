<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCodeEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidRequestParameterException extends HttpException
{
    public function __construct(string $message = 'Invalid request parameters.')
    {
        parent::__construct(HttpStatusCodeEnum::HTTP_BAD_REQUEST, $message);
    }
}
