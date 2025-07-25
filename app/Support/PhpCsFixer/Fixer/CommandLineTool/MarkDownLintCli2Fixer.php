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

use App\Support\PhpCsFixer\Fixer\CommandLineTool\Concerns\ReverseSuccessfulProcess;

/**
 * @see https://github.com/DavidAnson/markdownlint-cli2
 */
final class MarkDownLintCli2Fixer extends AbstractCommandLineToolFixer
{
    use ReverseSuccessfulProcess;

    #[\Override]
    protected function defaultCommand(): array
    {
        return ['markdownlint-cli2'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function requiredOptions(): array
    {
        return ['--fix', '--no-globs'];
    }

    #[\Override]
    protected function extensions(): array
    {
        return ['md', 'markdown'];
    }
}
