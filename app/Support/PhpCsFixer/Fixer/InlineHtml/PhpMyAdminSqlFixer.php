<?php

/** @noinspection PhpMissingParentCallCommonInspection */

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

use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpMyAdmin\SqlParser\Utils\Formatter;

/**
 * @see https://github.com/doctrine/sql-formatter
 * @see https://github.com/phpmyadmin/sql-parser
 */
final class PhpMyAdminSqlFixer extends AbstractInlineHtmlFixer
{
    public const string OPTIONS = 'options';

    #[\Override]
    protected function fixerOptions(): array
    {
        return [
            /**  @see \PhpMyAdmin\SqlParser\Utils\Formatter::getDefaultOptions() */
            (new FixerOptionBuilder(self::OPTIONS, 'The formatting options.'))
                ->setAllowedTypes(['array'])
                ->setDefault(['type' => 'text'])
                ->getOption(),
        ];
    }

    #[\Override]
    protected function defaultStart(): string
    {
        return <<<'HEADER_COMMENT'
            # noinspection SqlResolveForFile


            HEADER_COMMENT;
    }

    #[\Override]
    protected function supportedExtensions(): string
    {
        return 'sql';
    }

    #[\Override]
    protected function format(string $content): string
    {
        return Formatter::format($content, $this->configuration[self::OPTIONS]);
    }
}
