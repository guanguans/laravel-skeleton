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
