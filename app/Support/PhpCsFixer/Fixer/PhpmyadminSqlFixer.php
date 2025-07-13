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

use Illuminate\Support\Str;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpMyAdmin\SqlParser\Utils\Formatter;

/**
 * @see https://github.com/doctrine/sql-formatter
 * @see https://github.com/phpmyadmin/sql-parser
 */
final class PhpmyadminSqlFixer extends AbstractInlineHtmlFixer
{
    public const string HEADER_COMMENT = 'header_comment';
    public const string OPTIONS = 'options';

    #[\Override]
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(self::HEADER_COMMENT, 'The header comment to be prepended to the SQL string.'))
                ->setAllowedTypes(['string'])
                ->setDefault(
                    <<<'HEADER_COMMENT'
                        # noinspection SqlResolveForFile


                        HEADER_COMMENT,
                )
                ->getOption(),
            /**  @see \PhpMyAdmin\SqlParser\Utils\Formatter::getDefaultOptions() */
            (new FixerOptionBuilder(self::OPTIONS, 'The formatting options.'))
                ->setAllowedTypes(['array'])
                ->setDefault(['type' => 'text'])
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
            Formatter::format(
                Str::chopStart($content, $this->configuration[self::HEADER_COMMENT]),
                $this->configuration[self::OPTIONS]
            )
        )
            ->start($this->configuration[self::HEADER_COMMENT])
            // ->dd()
            ->toString();
    }
}
