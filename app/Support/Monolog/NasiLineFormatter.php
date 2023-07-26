<?php

declare(strict_types=1);

namespace App\Support\Monolog;

use Monolog\Formatter\LineFormatter;
use Monolog\Level;
use Monolog\LogRecord;
use function Termwind\render;

class NasiLineFormatter extends LineFormatter
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