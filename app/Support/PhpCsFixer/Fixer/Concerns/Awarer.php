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

trait Awarer
{
    protected \SplFileInfo $file;
    protected Tokens $tokens;

    /**
     * @noinspection SensitiveParameterInspection
     *
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    public function setFileAndTokens(\SplFileInfo $file, Tokens $tokens): void
    {
        $this->setFile($file);
        $this->setTokens($tokens);
    }

    public function setFile(\SplFileInfo $file): void
    {
        $this->file = $file;
    }

    /**
     * @noinspection SensitiveParameterInspection
     *
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    public function setTokens(Tokens $tokens): void
    {
        $this->tokens = $tokens;
    }
}
