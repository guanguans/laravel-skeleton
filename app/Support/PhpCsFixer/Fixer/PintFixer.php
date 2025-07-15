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

namespace App\Support\PhpCsFixer\Fixer;

use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Process\Process;
use function Illuminate\Support\php_binary;

/**
 * @see https://github.com/prettier/plugin-php/blob/main/docs/recipes/php-cs-fixer/PrettierPHPFixer.php
 * @see https://github.com/laravel/pint
 * @see https://github.com/super-linter/super-linter
 */
final class PintFixer extends AbstractToolFixer
{
    #[\Override]
    public function getPriority(): int
    {
        // Ensure pint process the code after php-cs-fixer.
        return \PHP_INT_MAX;
    }

    #[\Override]
    public function isRisky(): bool
    {
        return true;
    }

    #[\Override]
    public function supports(\SplFileInfo $file): bool
    {
        // Only support file that is php-cs-fixer's argument path.
        return parent::supports($file) && str($file)->contains(self::argv());
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    protected function isProcessSuccessful(Process $process, \SplFileInfo $file, Tokens $tokens): bool
    {
        return !$process->isSuccessful();
    }

    #[\Override]
    protected function defaultProgram(): array
    {
        return [php_binary(), 'vendor/bin/pint'];
    }

    protected function defaultArguments(): array
    {
        return [
            // '--ansi',
            // '--config=pint.json',
            // '--format=json',
            // '--output-format=txt',
            // '--output-to-file=.build/pint/.pint.output',
            // '--parallel',
            '--repair',
            '--silent',
            // '--test',
            // '-vv',
        ];
    }

    protected function defaultEnv(): ?array
    {
        return ['XDEBUG_MODE' => 'off'];
    }

    #[\Override]
    protected function supportsExtensions(): array
    {
        return ['php'];
    }
}
