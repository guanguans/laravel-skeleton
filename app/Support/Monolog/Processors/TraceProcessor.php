<?php

declare(strict_types=1);

namespace App\Support\Monolog\Processors;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

/**
 * ```
 * $monolog->pushProcessor(new TraceProcessor('trace-id'));
 * ```
 */
class TraceProcessor implements ProcessorInterface
{
    public function __construct(private string $traceId)
    {
    }

    public function __invoke(LogRecord $record): void
    {
        $extra = (array) $record['extra'];
        $extra['trace_id'] = $this->traceId;
        $record['extra'] = $extra;
    }
}
