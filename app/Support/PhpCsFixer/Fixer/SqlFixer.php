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

namespace App\Support\PhpCsFixer\Fixer;

use Doctrine\SqlFormatter\NullHighlighter;
use Doctrine\SqlFormatter\SqlFormatter;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ConfigurableFixerTrait;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @see https://github.com/doctrine/sql-formatter
 */
final class SqlFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    use ConfigurableFixerTrait;
    public const string INDENT_STRING = 'indent_string';

    #[\Override]
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            $summary = \sprintf('Format a [%s] file.', $this->getShortHeadlineName()),
            [new CodeSample($summary)]
        );
    }

    public static function name(): string
    {
        return (new self)->getName();
    }

    #[\Override]
    public function getName(): string
    {
        return \sprintf('User/%s', $this->getShortName());
    }

    public function getShortHeadlineName(): string
    {
        return str($this->getShortName())->headline()->toString();
    }

    public function getShortName(): string
    {
        return parent::getName();
    }

    #[\Override]
    public function isRisky(): bool
    {
        return true;
    }

    #[\Override]
    public function getPriority(): int
    {
        return \PHP_INT_MAX;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    #[\Override]
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->count() === 1 && $tokens[0]->isGivenKind(\T_INLINE_HTML);
    }

    #[\Override]
    public function supports(\SplFileInfo $file): bool
    {
        // return str_ends_with($file->getBasename(), 'blade.php');
        return $file->getExtension() === 'sql';
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(
                self::INDENT_STRING,
                'The SQL string with HTML styles and formatting wrapped in a <pre> tag.'
            ))
                ->setAllowedTypes(['string'])
                ->setDefault('    ')
                ->getOption(),
        ]);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     *
     * @throws \Throwable
     */
    #[\Override]
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $tokens[0] = new Token([\TOKEN_PARSE,
            $this->createSqlFormatter()->format($tokens[0]->getContent(), $this->configuration[self::INDENT_STRING]),
        ]);
    }

    private function createSqlFormatter(): SqlFormatter
    {
        static $sqlFormatter;

        return $sqlFormatter ??= new SqlFormatter(new NullHighlighter);
    }
}
