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
 * @see https://github.com/huacnlee/autocorrect
 */
final class AutocorrectFixer extends AbstractToolFixer
{
    #[\Override]
    protected function defaultTool(): array
    {
        return ['autocorrect'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function defaultArgs(): array
    {
        return ['--fix'];
    }

    #[\Override]
    protected function extensions(): array
    {
        return ['md', 'markdown', 'txt', 'text'];
    }
}
