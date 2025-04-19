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

namespace App\Support\Monolog\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

/**
 * ```
 * $monolog->pushProcessor(new TraceProcessor($extraData));
 * ```.
 *
 * @see https://github.com/creitive/monolog-extra-data-processor
 * @see https://github.com/WyriHaximus/php-monolog-processors
 */
readonly class AppendExtraDataProcessor implements ProcessorInterface
{
    public function __construct(private array $extraData) {}

    public function __invoke(LogRecord $record): LogRecord
    {
        $record['extra'] += $this->extraData;

        return $record;
    }
}
