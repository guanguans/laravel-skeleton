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

use Symfony\Component\HttpFoundation\Response;

final class BadRequestHttpException extends \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
{
    /**
     * @see self::fromStatusCode()
     *
     * @noinspection SensitiveParameterInspection
     */
    public function __construct(string $message = '', ?\Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct(
            $message ?: Response::$statusTexts[Response::HTTP_BAD_REQUEST],
            $previous,
            $code,
            $headers
        );
    }
}
