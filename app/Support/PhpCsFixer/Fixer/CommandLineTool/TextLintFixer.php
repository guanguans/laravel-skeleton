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
 * @see https://github.com/textlint/textlint
 */
final class TextLintFixer extends AbstractCommandLineToolFixer
{
    use PostPathCommand;

    #[\Override]
    protected function defaultCommand(): array
    {
        return ['textlint'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function requiredOptions(): array
    {
        return [
            '--fix',
            '--experimental',
        ];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function silentOptions(): array
    {
        return ['--no-color', '--quiet'];
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
