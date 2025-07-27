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

/**
 * @see https://github.com/hadolint/hadolint
 */
final class HadoLintFixer extends AbstractCommandLineToolFixer
{
    #[\Override]
    protected function defaultCommand(): array
    {
        return ['hadoint'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function requiredOptions(): array
    {
        return ['--fix'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function silentOptions(): array
    {
        return ['--quiet', '--no-diff-bg-color'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function debugOptions(): array
    {
        return ['--debug'];
    }

    #[\Override]
    protected function extensions(): array
    {
        return ['md', 'markdown', 'txt', 'text'];
    }
}
