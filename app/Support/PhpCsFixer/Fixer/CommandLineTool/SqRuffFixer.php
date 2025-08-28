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
 * @see https://github.com/quarylabs/sqruff
 */
final class SqRuffFixer extends AbstractCommandLineToolFixer
{
    #[\Override]
    protected function defaultExtensions(): array
    {
        return ['sql'];
    }

    #[\Override]
    protected function defaultCommand(): array
    {
        return ['sqruff', 'fix'];
    }

    #[\Override]
    protected function requiredOptions(): array
    {
        return [
            // '--dialect' => 'mysql',
        ];
    }
}
