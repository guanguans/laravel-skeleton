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

use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @see https://github.com/TheDragonCode/codestyler/blob/5.x/app/Fixers/JsonFixer.php
 * @see \Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer
 */
final class JsonFixer extends AbstractConfigurableFixer
{
    public const string DECODE_ASSOCIATIVE = 'decode_associative';
    public const string DECODE_FLAGS = 'decode_flags';
    public const string ENCODE_FLAGS = 'encode_flags';

    #[\Override]
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Format a JSON file.', [new CodeSample('Format a JSON file.')]);
    }

    #[\Override]
    public function getPriority(): int
    {
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
        return str($file->getExtension())->is('json', true);
    }

    #[\Override]
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(self::DECODE_ASSOCIATIVE, 'Whether to decode JSON as an associative array.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder(self::DECODE_FLAGS, 'The flags to use when decoding JSON.'))
                ->setAllowedTypes(['int'])
                ->setDefault(0)
                ->getOption(),
            (new FixerOptionBuilder(self::ENCODE_FLAGS, 'The flags to use when encoding JSON.'))
                ->setAllowedTypes(['int'])
                ->setDefault(\JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR)
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
        $tokens[0] = new Token([\TOKEN_PARSE, $this->convert($tokens[0]->getContent())]);
    }

    /**
     * @throws \JsonException
     */
    private function convert(string $content): string
    {
        return trim(json_encode(
            json_decode($content, $this->configuration[self::DECODE_ASSOCIATIVE], 512, \JSON_THROW_ON_ERROR | $this->configuration[self::DECODE_FLAGS]),
            \JSON_THROW_ON_ERROR | $this->configuration[self::ENCODE_FLAGS]
        ));
    }
}
