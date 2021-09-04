<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCodeEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidRepeatRequestException extends HttpException
{
    public function __construct()
    {
        parent::__construct(HttpStatusCodeEnum::HTTP_FORBIDDEN, 'Invalid repeat request.');
    }
}
