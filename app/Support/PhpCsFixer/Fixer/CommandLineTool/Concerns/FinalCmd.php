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

/**
 * @mixin \App\Support\PhpCsFixer\Fixer\CommandLineTool\AbstractCommandLineToolFixer
 */
trait FinalCmd
{
    protected function finalCmd(): string
    {
        return $this->configuration[self::CWD] ?? getcwd();
    }
}
