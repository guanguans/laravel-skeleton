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

use Illuminate\Support\Arr;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class EloquentLogHttpModelProcessor implements ProcessorInterface
{
    #[\Override]
    public function __invoke(LogRecord $record): LogRecord
    {
        return $record->with(context: Arr::only($record->context, [
            'method',
            'path',
            'request_header',
            'input',
            'response_header',
            'response',
            'ip',
            'duration',
        ]));
    }
}
