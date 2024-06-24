<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Monolog\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

/**
 * ```
 * $monolog->pushProcessor(new TraceProcessor($extraData));
 * ```
 *
 * @see https://github.com/creitive/monolog-extra-data-processor
 * @see https://github.com/WyriHaximus/php-monolog-processors
 */
class AppendExtraDataProcessor implements ProcessorInterface
{
    public function __construct(private readonly array $extraData) {}

    public function __invoke(LogRecord $record)
    {
        $record['extra'] += $this->extraData;

        return $record;
    }
}
