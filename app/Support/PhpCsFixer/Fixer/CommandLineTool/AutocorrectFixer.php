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
 * @see https://github.com/huacnlee/autocorrect
 */
final class AutocorrectFixer extends AbstractCommandLineToolFixer
{
    #[\Override]
    protected function defaultMainCommand(): array
    {
        return ['autocorrect'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function defaultOptions(): array
    {
        return ['--fix'];
    }

    #[\Override]
    protected function extensions(): array
    {
        return ['md', 'markdown', 'txt', 'text'];
    }
}
