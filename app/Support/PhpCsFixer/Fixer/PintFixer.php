<?php

/** @noinspection MissingParentCallInspection */
/** @noinspection PhpMissingParentCallCommonInspection */
/** @noinspection SensitiveParameterInspection */

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

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Process\Process;
use function Illuminate\Support\php_binary;
use function Psl\Filesystem\create_temporary_file;

/**
 * @see https://github.com/prettier/plugin-php/blob/main/docs/recipes/php-cs-fixer/PrettierPHPFixer.php
 */
final class PintFixer extends AbstractFixer
{
    #[\Override]
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Using Pint to format code.', [new CodeSample('Using Pint to format code.')]);
    }

    #[\Override]
    public function getPriority(): int
    {
        // Ensure pint post-process the code after php-cs-fixer.
        return -\PHP_INT_MAX;
    }

    #[\Override]
    public function isRisky(): bool
    {
        return true;
    }

    #[\Override]
    public function supports(\SplFileInfo $file): bool
    {
        // Only specific single file is supported.
        return str($file)->contains(self::argv());
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    #[\Override]
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $process = new Process([
            php_binary(),
            'vendor/bin/pint',
            $path = self::pathFor($file, $tokens),
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
        ]);
        $process->setEnv(['XDEBUG_MODE' => 'off'])->run();
        $process->isSuccessful() or $tokens->setCode(file_get_contents($path));
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    private static function pathFor(\SplFileInfo $file, Tokens $tokens): string
    {
        $path = (string) $file;

        if (self::isDryRun()) {
            file_put_contents($path = self::createTemporaryFile(), $tokens->generateCode());
        }

        return $path;
    }

    private static function isDryRun(): bool
    {
        return \in_array('--dry-run', self::argv(), true);
    }

    /**
     * @noinspection GlobalVariableUsageInspection
     */
    private static function argv(): array
    {
        return $_SERVER['argv'] ?? [];
    }

    private static function createTemporaryFile(): string
    {
        static $temporaryFile;

        return $temporaryFile ??= create_temporary_file();
    }
}
