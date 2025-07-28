<?php

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

use Doctrine\SqlFormatter\NullHighlighter;
use Doctrine\SqlFormatter\SqlFormatter;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;

/**
 * @see https://github.com/doctrine/sql-formatter
 * @see https://github.com/phpmyadmin/sql-parser
 */
final class DoctrineSqlFixer extends AbstractInlineHtmlFixer
{
    public const string INDENT_STRING = 'indent_string';

    #[\Override]
    protected function fixerOptions(): array
    {
        return [
            (new FixerOptionBuilder(
                self::INDENT_STRING,
                'The SQL string with HTML styles and formatting wrapped in a <pre> tag.'
            ))
                ->setAllowedTypes(['string'])
                ->setDefault('    ')
                ->getOption(),
        ];
    }

    #[\Override]
    protected function defaultExtensions(): array
    {
        return ['sql'];
    }

    #[\Override]
    protected function format(string $content): string
    {
        return $this->createSqlFormatter()->format($content, $this->configuration[self::INDENT_STRING]);
    }

    private function createSqlFormatter(): SqlFormatter
    {
        static $sqlFormatter;

        return $sqlFormatter ??= new SqlFormatter(new NullHighlighter);
    }
}
