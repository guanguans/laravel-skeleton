<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCodeEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BadRequestException extends HttpException
{
    public function __construct(string $message = 'Bad request.')
    {
        parent::__construct(HttpStatusCodeEnum::HTTP_BAD_REQUEST, $message);
    }
}
