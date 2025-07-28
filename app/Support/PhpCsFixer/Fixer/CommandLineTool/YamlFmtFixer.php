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
 * @see https://github.com/google/yamlfmt
 */
final class YamlFmtFixer extends AbstractCommandLineToolFixer
{
    #[\Override]
    protected function defaultCommand(): array
    {
        return ['yamlfmt'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function requiredOptions(): array
    {
        return ['-gitignore_excludes'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function silentOptions(): array
    {
        return ['-quiet'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function debugOptions(): array
    {
        return ['-debug', '-lint', '-print_conf', '-verbose'];
    }

    #[\Override]
    protected function defaultExtensions(): array
    {
        return ['yaml', 'yml'];
    }
}
