<?php

/** @noinspection MissingParentCallInspection */
/** @noinspection PhpMissingParentCallCommonInspection */

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support;

use Illuminate\Console\OutputStyle;
use Symfony\Component\Process\Process;

/**
 * @see https://github.com/tighten/lambo/blob/main/app/ConsoleWriter.php
 */
class ConsoleWriter extends OutputStyle
{
    final public const BLUE = 'fg=blue';

    final public const GREEN = 'fg=green';

    final public const RED = 'fg=red';

    final public const MAGENTA = 'fg=magenta';

    public static function formatString(string $string, string $format): string
    {
        return "<{$format}>{$string}</>";
    }

    public function panel(string $prefix, string $message, string $style): void
    {
        parent::block($message, $prefix, $style, ' ', true, false);
    }

    public function sectionTitle($sectionTitle): void
    {
        $this->newLine();
        $this->text([
            "<fg=yellow;bg=default>{$sectionTitle}</>",
            '<fg=yellow;bg=default>'.str_repeat('#', \strlen($sectionTitle)).'</>',
        ]);
    }

    public function logStep($message): void
    {
        parent::block($message, null, 'fg=yellow;bg=default', ' // ', false, false);
    }

    public function exec(string $command): void
    {
        $this->labeledLine('EXEC', $command, 'bg=blue;fg=black');
    }

    public function success($message, $label = 'PASS'): void
    {
        $this->labeledLine($label, $message, 'fg=black;bg=green');
    }

    public function ok($message): void
    {
        $this->success($message, ' OK ');
    }

    public function note($message, $label = 'NOTE'): void
    {
        $this->labeledLine($label, $message, 'fg=black;bg=yellow');
    }

    public function warn($message, $label = 'WARN'): void
    {
        $this->labeledLine($label, "<fg=red;bg=default>{$message}</>", 'fg=black;bg=red');
    }

    public function warnCommandFailed($command): void
    {
        $this->warn("Failed to run {$command}");
    }

    public function showOutputErrors(string $errors): void
    {
        parent::text([
            '<fg=red;bg=default>--------------------------------------------------------------------------------',
            str_replace(PHP_EOL, PHP_EOL.' ', trim($errors)),
            '--------------------------------------------------------------------------------</>',
        ]);
    }

    public function showOutput(string $errors): void
    {
        parent::text([
            '--------------------------------------------------------------------------------',
            str_replace(PHP_EOL, PHP_EOL.' ', trim($errors)),
            '--------------------------------------------------------------------------------',
        ]);
    }

    public function exception($message): void
    {
        parent::block($message, null, 'fg=black;bg=red', ' ', true, false);
    }

    public function text(array|string $message): void
    {
        parent::text($message);
    }

    public function listing(array $items): void
    {
        parent::newLine();
        $text = collect($items)->map(static fn ($dependency): string => '  - '.$dependency)->toArray();
        parent::text($text);
        parent::newLine();
    }

    public function table(array $columnHeadings, array $rowData): void
    {
        parent::table($columnHeadings, $rowData);
    }

    public function consoleOutput(string $line, $type): void
    {
        if (config('lambo.store.with_output')) {
            (Process::ERR === $type)
                ? $this->labeledLine('!️', '┃ '.$line, 'fg=yellow')
                : $this->labeledLine('✓︎', '┃ '.$line, 'fg=green;');
        }
    }

    public function labeledLine(string $label, string $message, string $labelFormat = 'fg=default;bg=default', int $indentColumns = 0): void
    {
        $indent = str_repeat(' ', $indentColumns);
        $this->isDecorated()
            ? parent::text("{$indent}<{$labelFormat}> {$label} </> {$message}")
            : parent::text("{$indent}[ {$label} ] {$message}");
    }
}
