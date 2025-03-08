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

namespace App\Support;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\OutputStyle;

/**
 * @see https://github.com/matchish/laravel-scout-elasticsearch/blob/master/src/Console/Commands/ProgressBarFactory.php
 */
class ProgressBarFactory
{
    public function __construct(/** @var OutputStyle */
        private readonly OutputStyle $output
    ) {}

    public function create(int $max = 0): ProgressBar
    {
        $bar = $this->output->createProgressBar($max);
        $bar->setBarCharacter('<fg=green>⚬</>');
        $bar->setEmptyBarCharacter('<fg=red>⚬</>');
        $bar->setProgressCharacter('<fg=green>➤</>');
        $bar->setRedrawFrequency(1);
        $bar->maxSecondsBetweenRedraws(0);
        $bar->minSecondsBetweenRedraws(0);
        $bar->setFormat(
            "%message%\n%current%/%max% [%bar%] %percent:3s%%\n"
        );

        return $bar;
    }
}
