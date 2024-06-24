<?php

declare(strict_types=1);

namespace App\Support\Monolog\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

class EloquentHandler extends AbstractProcessingHandler
{
    /**
     * @param  class-string  $modelClass
     */
    public function __construct(
        private readonly string $modelClass,
        int|string|Level $level = Level::Debug,
        bool $bubble = true
    ) {
        parent::__construct($level, $bubble);
    }

    protected function write(LogRecord $record): void
    {
        $this->modelClass::query()->create($record->formatted);
    }
}
