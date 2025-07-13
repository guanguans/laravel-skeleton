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

use Doctrine\SqlFormatter\NullHighlighter;
use Doctrine\SqlFormatter\SqlFormatter;
use Illuminate\Support\Str;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;

/**
 * @see https://github.com/doctrine/sql-formatter
 * @see https://github.com/phpmyadmin/sql-parser
 */
final class DoctrineSqlFixer extends AbstractInlineHtmlFixer
{
    public const string HEADER_COMMENT = 'header_comment';
    public const string INDENT_STRING = 'indent_string';

    /**
     * @see \PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer::getPriority()
     */
    #[\Override]
    public function getPriority(): int
    {
        return -98;
    }

    #[\Override]
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(self::HEADER_COMMENT, 'The header comment to be prepended to the SQL string.'))
                ->setAllowedTypes(['string'])
                ->setDefault(
                    <<<'HEADER_COMMENT'
                        # noinspection SqlResolveForFile


                        HEADER_COMMENT
                )
                ->getOption(),
            (new FixerOptionBuilder(self::INDENT_STRING, 'The SQL string with HTML styles and formatting wrapped in a <pre> tag.'))
                ->setAllowedTypes(['string'])
                ->setDefault('    ')
                ->getOption(),
        ]);
    }

    #[\Override]
    protected function supportedExtensions(): iterable|string
    {
        return 'sql';
    }

    #[\Override]
    protected function format(string $content): string
    {
        return str(
            $this->createSqlFormatter()->format(
                Str::chopStart($content, $this->configuration[self::HEADER_COMMENT]),
                $this->configuration[self::INDENT_STRING]
            )
        )
            ->start($this->configuration[self::HEADER_COMMENT])
            // ->dd()
            ->toString();
    }

    private function createSqlFormatter(): SqlFormatter
    {
        static $sqlFormatter;

        return $sqlFormatter ??= new SqlFormatter(new NullHighlighter);
    }
}
