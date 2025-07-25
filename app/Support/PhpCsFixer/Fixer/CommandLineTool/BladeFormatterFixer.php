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
 * @see https://github.com/shufo/blade-formatter
 */
final class BladeFormatterFixer extends AbstractCommandLineToolFixer
{
    #[\Override]
    protected function defaultCommand(): array
    {
        return ['blade-formatter'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function requiredOptions(): array
    {
        return ['--write'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function debugOptions(): array
    {
        return ['--diff', '--progress'];
    }

    #[\Override]
    protected function extensions(): array
    {
        return ['blade.php'];
    }
}
