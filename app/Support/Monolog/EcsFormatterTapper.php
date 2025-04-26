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

namespace App\Support\Monolog;

use Hamidrezaniazi\Pecs\Monolog\EcsFormatter;
use Monolog\Handler\FormattableHandlerInterface;
use Monolog\Logger;

final class EcsFormatterTapper
{
    public function __invoke(Logger $logger): void
    {
        foreach ($logger->getHandlers() as $handler) {
            if ($handler instanceof FormattableHandlerInterface) {
                $handler->setFormatter(app(EcsFormatter::class));
            }
        }
    }
}
