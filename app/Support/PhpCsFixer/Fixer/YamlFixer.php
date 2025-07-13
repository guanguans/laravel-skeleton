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
use Symfony\Component\Yaml\Yaml;

/**
 * @see https://github.com/TheDragonCode/codestyler/blob/5.x/app/Fixers/JsonFixer.php
 * @see \Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer
 */
final class YamlFixer extends AbstractConfigurableFixer
{
    public const string PARSE_FLAGS = 'parse_flags';
    public const string DUMP_INLINE = 'dump_inline';
    public const string DUMP_INDENT = 'dump_indent';
    public const string DUMP_FLAGS = 'dump_flags';

    #[\Override]
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Format a YAML file.', [new CodeSample('Format a YAML file.')]);
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

    #[\Override]
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->count() === 1 && $tokens[0]->isGivenKind(\T_INLINE_HTML);
    }

    #[\Override]
    public function supports(\SplFileInfo $file): bool
    {
        return str($file->getExtension())->is(['yaml', 'yml'], true) && !str(file_get_contents((string) $file))->contains('#');
    }

    #[\Override]
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(self::PARSE_FLAGS, 'A bit field of PARSE_* constants to customize the YAML parser behavior.'))
                ->setAllowedTypes(['int'])
                ->setDefault(0)
                ->getOption(),
            (new FixerOptionBuilder(self::DUMP_INLINE, 'The level where you switch to inline YAML.'))
                ->setAllowedTypes(['int'])
                ->setDefault(\PHP_INT_MAX)
                ->getOption(),
            (new FixerOptionBuilder(self::DUMP_INDENT, 'The amount of spaces to use for indentation of nested nodes.'))
                ->setAllowedTypes(['int'])
                ->setDefault(2)
                ->getOption(),
            (new FixerOptionBuilder(self::DUMP_FLAGS, 'A bit field of DUMP_* constants to customize the dumped YAML string.'))
                ->setAllowedTypes(['int'])
                ->setDefault(
                    Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE
                    | Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK
                    | Yaml::DUMP_NULL_AS_EMPTY
                )
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
     * @noinspection PhpMemberCanBePulledUpInspection
     */
    private function convert(string $content): string
    {
        return trim(Yaml::dump(
            Yaml::parse($content, $this->configuration[self::PARSE_FLAGS]),
            $this->configuration[self::DUMP_INLINE],
            $this->configuration[self::DUMP_INDENT],
            $this->configuration[self::DUMP_FLAGS]
        ));
    }
}
