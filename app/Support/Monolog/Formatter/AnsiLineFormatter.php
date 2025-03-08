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
 * ```.
 */
class AnsiLineFormatter extends LineFormatter
{
    #[\Override]
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

        render(\sprintf($html, parent::format($record)));

        return '';
    }
}
