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
 * @see https://github.com/textlint/textlint
 */
final class TextLintFixer extends AbstractCommandLineToolFixer
{
    #[\Override]
    protected function defaultExtensions(): array
    {
        return ['md', 'markdown', 'txt', 'text'];
    }

    #[\Override]
    protected function defaultCommand(): array
    {
        return ['textlint'];
    }

    #[\Override]
    protected function requiredOptions(): array
    {
        return ['--fix', '--experimental'];
    }
}
