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

use Symfony\Component\Process\Process;

/**
 * @see https://github.com/DavidAnson/markdownlint-cli2
 */
final class MarkdownLintCli2Fixer extends AbstractCommandLineToolFixer
{
    protected function isSuccessfulProcess(Process $process): bool
    {
        return parent::isSuccessfulProcess($process) || $process->getExitCode() === 1;
    }

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
    protected function defaultExtensions(): array
    {
        return ['md', 'markdown'];
    }
}
