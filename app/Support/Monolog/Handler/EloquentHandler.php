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

namespace App\Support\Monolog\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

final class EloquentHandler extends AbstractProcessingHandler
{
    /**
     * @param class-string $modelClass
     */
    public function __construct(
        private readonly string $modelClass,
        int|Level|string $level = Level::Debug,
        bool $bubble = true
    ) {
        parent::__construct($level, $bubble);
    }

    #[\Override]
    protected function write(LogRecord $record): void
    {
        $this->modelClass::query()->create($record->formatted);
    }
}
