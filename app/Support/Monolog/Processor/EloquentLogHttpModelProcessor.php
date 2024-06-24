<?php

declare(strict_types=1);

namespace App\Support\Monolog\Processor;

use Illuminate\Support\Arr;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class EloquentLogHttpModelProcessor implements ProcessorInterface
{
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
