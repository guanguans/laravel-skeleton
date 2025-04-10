<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

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
