<?php

/** @noinspection MissingParentCallInspection */
/** @noinspection PhpInternalEntityUsedInspection */
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

namespace App\Support\PhpCsFixer;

use App\Support\Console\SymfonyStyleFactory;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Process\Process;

/**
 * @see https://github.com/prettier/plugin-php/blob/main/docs/recipes/php-cs-fixer/PrettierPHPFixer.php
 * @see \ErickSkrauch\PhpCsFixer\Fixer\AbstractFixer
 * @see \PhpCsFixer\AbstractFixer
 * @see \PhpCsFixerCustomFixers\Fixer
 * @see \PhpCsFixerCustomFixers\Fixer\AbstractFixer
 */
final class PintFixer extends AbstractFixer
{
    #[\Override]
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::class, [new CodeSample(self::class)]);
    }

    public static function name(): string
    {
        return 'User/pint';
    }

    #[\Override]
    public function getName(): string
    {
        return self::name();
    }

    #[\Override]
    public function getPriority(): int
    {
        return -\PHP_INT_MAX;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    #[\Override]
    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }

    #[\Override]
    public function isRisky(): bool
    {
        return true;
    }

    /**
     * @noinspection GlobalVariableUsageInspection
     *
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    #[\Override]
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        // $tokens->setCode($code);

        $command = [
            'vendor/bin/pint',
            (string) $file,
            '--output-to-file=.build/pint/.pint.output',
            '--output-format=txt',
            // '--format=json',
            // '--parallel',
            // '--test',
            // '--ansi',
            '-v',
        ];

        if (\in_array('--dry-run', $_SERVER['argv'] ?? [], true)) {
            $command[] = '--test';
        }

        $process = new Process(command: $command, env: ['XDEBUG_MODE' => 'off']);

        $symfonyStyle = SymfonyStyleFactory::create();

        $process->run(static function (string $type, string $line) use ($symfonyStyle): void {
            $symfonyStyle->write(Process::ERR === $type ? "</error>$line</error>" : "<info>$line</info>");
        });
    }
}
