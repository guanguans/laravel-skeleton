<?php

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
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
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

    #[\Override]
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            $summary = "Format a [{$this->getShortHeadlineName()}] file.",
            [new CodeSample($summary)]
        );
    }

    #[\Override]
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([$this->extensionsFixerOption(), ...$this->fixerOptions()]);
    }

    /**
     * @noinspection PhpMemberCanBePulledUpInspection
     *
     * @return list<\PhpCsFixer\FixerConfiguration\FixerOptionInterface>
     */
    abstract protected function fixerOptions(): array;

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     *
     * @throws \Throwable
     */
    #[\Override]
    final protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $tokens[0] = new Token([\TOKEN_PARSE, $this->format($tokens[0]->getContent())]);
    }

    abstract protected function format(string $content): string;
}
