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

use PhpCsFixer\Tokenizer\Tokens;

/**
 * @see https://github.com/mvdan/sh
 */
final class ShFmtFixer extends AbstractToolFixer
{
    #[\Override]
    protected function defaultTool(): array
    {
        return ['shfmt'];
    }

    // /**
    //  * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
    //  */
    // protected function args(\SplFileInfo $file, Tokens $tokens): array
    // {
    //     return [...$this->configuration[self::ARGS], $this->path($file, $tokens)];
    // }

    protected function defaultArgs(): array
    {
        return ['-l', '-w'];
    }

    #[\Override]
    protected function extensions(): array
    {
        return ['sh', 'bash', 'ksh', 'zsh', 'fish'];
    }
}
