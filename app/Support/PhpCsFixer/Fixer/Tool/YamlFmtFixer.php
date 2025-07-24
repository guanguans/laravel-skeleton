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

namespace App\Support\PhpCsFixer\Fixer\Tool;

/**
 * @see https://github.com/google/yamlfmt
 */
final class YamlFmtFixer extends AbstractToolFixer
{
    #[\Override]
    protected function defaultTool(): array
    {
        return ['yamlfmt'];
    }

    #[\Override]
    protected function extensions(): array
    {
        return ['yaml', 'yml'];
    }
}
