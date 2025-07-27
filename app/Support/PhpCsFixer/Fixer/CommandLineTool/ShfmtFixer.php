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

namespace App\Support\PhpCsFixer\Fixer\CommandLineTool;

use App\Support\PhpCsFixer\Fixer\CommandLineTool\Concerns\PostPathCommand;

/**
 * @see https://github.com/mvdan/sh
 */
final class ShfmtFixer extends AbstractCommandLineToolFixer
{
    use PostPathCommand;

    #[\Override]
    protected function defaultCommand(): array
    {
        return ['shfmt'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function requiredOptions(): array
    {
        return [
            '--write',
            // '--simplify',
            // '--minify',
        ];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function debugOptions(): array
    {
        return ['--list'];
    }

    /**
     * @see `-ln, --language-dialect str  bash/posix/mksh/bats, default "auto"`
     */
    #[\Override]
    protected function extensions(): array
    {
        return ['sh', 'bats'];
    }
}
