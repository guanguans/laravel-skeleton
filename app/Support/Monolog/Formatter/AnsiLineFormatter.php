<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Monolog\Formatter;

use Monolog\Formatter\LineFormatter;
use Monolog\Level;
use Monolog\LogRecord;

use function Termwind\render;

/**
 * ```php
 * if ($handler instanceof \Monolog\Handler\FormattableHandlerInterface) {
 *     $handler->setFormatter(new AnsiLineFormatter());
 * }
 * ```
 */
class AnsiLineFormatter extends LineFormatter
{
    public function format(LogRecord $record): string
    {
        $html = match ($record->level->value) {
            Level::Debug->value => '<div class="text-white">%s</div>',
            Level::Info->value => '<div class="text-green">%s</div>',
            Level::Notice->value => '<div class="text-cyan">%s</div>',
            Level::Warning->value => '<div class="text-yellow">%s</div>',
            Level::Error->value => '<div class="text-red bg-black">%s</div>',
            Level::Critical->value => '<div class="text-red bg-white">%s</div>',
            Level::Alert->value => '<div class="text-black bg-red">%s</div>',
            Level::Emergency->value => '<div class="text-white bg-red">%s</div>',
        };

        render(sprintf($html, parent::format($record)));

        return '';
    }
}
