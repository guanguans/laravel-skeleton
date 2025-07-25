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

namespace App\Support\PhpCsFixer\Fixer\CommandLineTool\Concerns;

use Symfony\Component\Process\Process;

/**
 * @mixin \App\Support\PhpCsFixer\Fixer\CommandLineTool\AbstractCommandLineToolFixer
 */
trait ReverseSuccessfulProcess
{
    #[\Override]
    protected function isSuccessfulProcess(Process $process): bool
    {
        return !$process->isSuccessful();
    }
}
