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

namespace App\Support\PhpCsFixer\Fixer\Concerns;

use PhpCsFixer\Tokenizer\Tokens;

trait InlineHtmlCandidate
{
    /**
     * @see \PhpCsFixer\Tokenizer\Tokens::isMonolithicPhp()
     *
     * @noinspection SensitiveParameterInspection
     *
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    #[\Override]
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->count() === 1 && $tokens[0]->isGivenKind(\T_INLINE_HTML);
    }
}
