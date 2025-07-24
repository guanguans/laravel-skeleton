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
 * @see https://github.com/shufo/blade-formatter
 */
final class BladeFormatterFixer extends AbstractToolFixer
{
    #[\Override]
    protected function defaultTool(): array
    {
        return ['blade-formatter'];
    }

    protected function defaultArgs(): array
    {
        return ['-w'];
    }

    #[\Override]
    protected function extensions(): array
    {
        return ['blade.php'];
    }
}
