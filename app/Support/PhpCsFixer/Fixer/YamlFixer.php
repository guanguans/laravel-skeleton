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
use Symfony\Component\Yaml\Yaml;

/**
 * @see https://github.com/TheDragonCode/codestyler/blob/5.x/app/Fixers/YamlFixer.php
 * @see https://github.com/symfony/yaml
 */
final class YamlFixer extends AbstractInlineHtmlFixer
{
    public const string PARSE_FLAGS = 'parse_flags';
    public const string DUMP_INLINE = 'dump_inline';
    public const string DUMP_INDENT = 'dump_indent';
    public const string DUMP_FLAGS = 'dump_flags';

    #[\Override]
    public function supports(\SplFileInfo $file): bool
    {
        return parent::supports($file) && !str(file_get_contents((string) $file))->contains('#');
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

    #[\Override]
    protected function supportedExtensions(): array
    {
        return ['yaml', 'yml'];
    }

    #[\Override]
    protected function format(string $content): string
    {
        return Yaml::dump(
            Yaml::parse($content, $this->configuration[self::PARSE_FLAGS]),
            $this->configuration[self::DUMP_INLINE],
            $this->configuration[self::DUMP_INDENT],
            $this->configuration[self::DUMP_FLAGS]
        );
    }
}
