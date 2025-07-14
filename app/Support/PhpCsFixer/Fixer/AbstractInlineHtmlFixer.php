<?php

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

use Illuminate\Support\Stringable;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

abstract class AbstractInlineHtmlFixer extends AbstractConfigurableFixer
{
    public const string START = 'start';
    public const string FINISH = 'finish';

    #[\Override]
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            $summary = \sprintf('Format a %s file.', str($this->getSortName())->headline()->lower()),
            [new CodeSample($summary)]
        );
    }

    /**
     * @see \PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer::getPriority()
     */
    #[\Override]
    public function getPriority(): int
    {
        return -99;
    }

    #[\Override]
    public function isRisky(): bool
    {
        return true;
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
        return str($file->getExtension())->is($this->supportedExtensions(), true)
            || str($file->getPathname())->lower()->endsWith($this->supportedExtensions());
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     *
     * @throws \Throwable
     */
    #[\Override]
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $tokens[0] = new Token([
            \TOKEN_PARSE,
            str(
                $this->format(
                    str($tokens[0]->getContent())
                        ->trim()
                        ->when(
                            $this->configuration[self::START],
                            static fn (Stringable $content, string $start) => $content->chopStart($start)
                        )
                        ->when(
                            $this->configuration[self::FINISH],
                            static fn (Stringable $content, string $finish) => $content->chopEnd($finish)
                        )
                        // ->dd()
                        ->toString()
                )
            )
                ->trim()
                ->start($this->configuration[self::START])
                ->finish($this->configuration[self::FINISH])
                // ->dd()
                ->toString(),
        ]);
    }

    #[\Override]
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([...$this->defaultFixerOptions(), ...$this->fixerOptions()]);
    }

    protected function defaultStart(): string
    {
        return '';
    }

    protected function defaultFinish(): string
    {
        return '';
    }

    /**
     * @return list<\PhpCsFixer\FixerConfiguration\FixerOptionInterface>
     */
    protected function defaultFixerOptions(): array
    {
        return [
            (new FixerOptionBuilder(self::START, 'The header comment to be prepended to the content.'))
                ->setAllowedTypes(['string'])
                ->setDefault($this->defaultStart())
                ->getOption(),
            (new FixerOptionBuilder(self::FINISH, 'The footer comment to be appended to the content.'))
                ->setAllowedTypes(['string'])
                ->setDefault($this->defaultFinish())
                ->getOption(),
        ];
    }

    /**
     * @return list<\PhpCsFixer\FixerConfiguration\FixerOptionInterface>
     */
    abstract protected function fixerOptions(): array;

    /**
     * @return iterable<string>|string
     */
    abstract protected function supportedExtensions(): iterable|string;

    abstract protected function format(string $content): string;
}
