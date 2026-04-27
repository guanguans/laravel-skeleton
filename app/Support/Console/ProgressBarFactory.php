<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Console;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\OutputStyle;

/**
 * @see https://github.com/matchish/laravel-scout-elasticsearch/blob/master/src/Console/Commands/ProgressBarFactory.php
 */
final readonly class ProgressBarFactory
{
    public function __construct(private OutputStyle $outputStyle) {}

    public function create(int $max = 0): ProgressBar
    {
        $progressBar = $this->outputStyle->createProgressBar($max);
        $progressBar->setBarCharacter('<fg=green>⚬</>');
        $progressBar->setEmptyBarCharacter('<fg=red>⚬</>');
        $progressBar->setProgressCharacter('<fg=green>➤</>');
        $progressBar->setRedrawFrequency(1);
        $progressBar->maxSecondsBetweenRedraws(0);
        $progressBar->minSecondsBetweenRedraws(0);
        $progressBar->setFormat("%message%\n%current%/%max% [%bar%] %percent:3s%%\n");

        return $progressBar;
    }
}
