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

namespace App\Support\PhpCsFixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for using prettier-php to fix.
 */
final class PrettierPHPFixer implements FixerInterface
{
    /**
     * {@inheritDoc}
     */
    public function getPriority(): int
    {
        // Allow prettier to pre-process the code before php-cs-fixer
        return 999;
    }

    /**
     * {@inheritDoc}
     *
     * @noinspection SensitiveParameterInspection
     *
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isRisky(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @noinspection SensitiveParameterInspection
     *
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    public function fix(\SplFileInfo $file, Tokens $tokens): void
    {
        if (
            0 < $tokens->count()
            && $this->isCandidate($tokens)
            && $this->supports($file)
        ) {
            $this->applyFix($file, $tokens);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'Prettier/php';
    }

    /**
     * {@inheritDoc}
     */
    public function supports(\SplFileInfo $file): bool
    {
        return true;
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Overridden and implemented methods must be sorted in the same order as they are defined in parent classes.',
            [
                new CodeSample(<<<'EOD'
                    <?php
                    class Foo implements Serializable {

                        public function unserialize($data) {}

                        public function serialize() {}

                    }

                    EOD),
            ],
        );
    }

    /**
     * @noinspection SensitiveParameterInspection
     *
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    private function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        exec("yarn exec -- prettier $file", $prettierOutput);
        $code = implode(\PHP_EOL, $prettierOutput);
        $tokens->setCode($code);
    }
}
