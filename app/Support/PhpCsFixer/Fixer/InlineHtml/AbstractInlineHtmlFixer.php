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

namespace App\Support\PhpCsFixer\Fixer\InlineHtml;

use App\Support\PhpCsFixer\Fixer\AbstractConfigurableFixer;
use App\Support\PhpCsFixer\Fixer\Concerns\AllowRisky;
use App\Support\PhpCsFixer\Fixer\Concerns\HighestPriority;
use App\Support\PhpCsFixer\Fixer\Concerns\InlineHtmlCandidate;
use App\Support\PhpCsFixer\Fixer\Concerns\SupportsExtensions;
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
    use AllowRisky;
    use HighestPriority;
    use InlineHtmlCandidate;
    use SupportsExtensions;
    public const string START = 'start';
    public const string FINISH = 'finish';

    #[\Override]
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            $summary = \sprintf('Format a [%s] file.', $this->getSortHeadlineName()),
            [new CodeSample($summary)]
        );
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     *
     * @throws \Throwable
     */
    #[\Override]
    final protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
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

    protected function defaultStart(): string
    {
        return '';
    }

    protected function defaultFinish(): string
    {
        return '';
    }

    /**
     * @noinspection PhpMemberCanBePulledUpInspection
     *
     * @return list<\PhpCsFixer\FixerConfiguration\FixerOptionInterface>
     */
    abstract protected function fixerOptions(): array;

    abstract protected function format(string $content): string;
}
