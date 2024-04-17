<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Monolog;

use Hamidrezaniazi\Pecs\Monolog\EcsFormatter;
use Illuminate\Log\Logger;
use Monolog\Handler\FormattableHandlerInterface;

class EcsFormatterTapper
{
    public function __invoke(Logger $logger): void
    {
        foreach ($logger->getHandlers() as $handler) {
            \assert($handler instanceof FormattableHandlerInterface);
            $handler->setFormatter(app(EcsFormatter::class));
        }
    }
}
