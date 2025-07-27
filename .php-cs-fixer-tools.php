<?php

/** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

use App\Support\PhpCsFixer\Fixer\CommandLineTool\AbstractCommandLineToolFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\AutocorrectFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\BladeFormatterFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\DotEnvLinterFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\LintMdFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\MarkDownLintCli2Fixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\PintFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\ShFmtFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\SqlFluffFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\TextLintFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\XmlLintFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\YamlFmtFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\ZhLintFixer;
use App\Support\PhpCsFixer\Fixer\InlineHtml\DoctrineSqlFixer;
use App\Support\PhpCsFixer\Fixer\InlineHtml\JsonFixer;
use App\Support\PhpCsFixer\Fixer\InlineHtml\NeonFixer;
use App\Support\PhpCsFixer\Fixer\InlineHtml\PhpMyAdminSqlFixer;
use App\Support\PhpCsFixer\Fixer\InlineHtml\XmlFixer;
use App\Support\PhpCsFixer\Fixer\InlineHtml\YamlFixer;
use PhpCsFixer\Config;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use Symfony\Component\Finder\Finder;

/** @see https://github.com/laravel/pint/blob/main/resources/presets */
return (new Config)
    ->registerCustomFixers($userFixers = iterator_to_array(new App\Support\PhpCsFixer\Fixers))
    ->setRules(array_reduce(
        array_filter($userFixers, static fn (App\Support\PhpCsFixer\Fixer\AbstractFixer $fixer): bool => !\in_array(
            $fixer->getName(),
            [
                // AutocorrectFixer::name(),
                // LintMdFixer::name(),
                // MarkDownLintCli2Fixer::name(),
                TextLintFixer::name(),
                ZhLintFixer::name(),

                // PintFixer::name(),
                // BladeFormatterFixer::name(),

                // DotEnvLinterFixer::name(),

                // ShFmtFixer::name(),

                // DoctrineSqlFixer::name(),
                PhpMyAdminSqlFixer::name(),
                SqlFluffFixer::name(),

                YamlFixer::name(),
                // YamlFmtFixer::name(),

                XmlFixer::name(),
                // XmlLintFixer::name(),

                // JsonFixer::name(),

                NeonFixer::name(),
            ],
            true
        )),
        static function (array $rules, App\Support\PhpCsFixer\Fixer\AbstractFixer $fixer): array {
            $rules[$fixer->getName()] = true;

            return $rules;
        },
        [
            // '@PhpCsFixer' => true,
            'single_blank_line_at_eof' => true,
            // SqlFluffFixer::name() => [
            //     AbstractCommandLineToolFixer::OPTIONS => [
            //         '--dialect' => 'mysql',
            //     ],
            //     SqlFluffFixer::EXTENSIONS => ['sql'],
            // ],
        ]
    ))
    ->setFinder(
        Finder::create()
            ->in([
                __DIR__.'/.github/',
                // __DIR__.'/.tinker/',
                // __DIR__.'/app/',
                // __DIR__.'/bootstrap/',
                // __DIR__.'/config/',
                __DIR__.'/database/',
                // __DIR__.'/public/',
                __DIR__.'/resources/',
                // __DIR__.'/routes/',
                __DIR__.'/tests/',
            ])
            ->exclude([
                'cache/',
                'Fixtures/',
            ])
            ->notPath([
                '/lang\/.*\.json$/',
            ])
            ->name([
                '/\.bats$/',
                '/\.env$/',
                '/\.env\.example$/',
                '/\.json$/',
                '/\.markdown$/',
                '/\.md$/',
                '/\.neon$/',
                '/\.sh$/',
                '/\.sql$/',
                '/\.text$/',
                '/\.txt$/',
                '/\.xml$/',
                '/\.xml\.dist$/',
                '/\.yaml$/',
                '/\.yml$/',
            ])
            ->notName([
                '/\.lock$/',
                '/\.php$/',
            ])
            ->ignoreDotFiles(false)
            ->ignoreUnreadableDirs(false)
            ->ignoreVCS(true)
            ->ignoreVCSIgnored(true)
            ->files()
            ->append(
                Finder::create()
                    ->in([
                        __DIR__,
                    ])
                    ->notName([
                        '/\.lock$/',
                        '/\.php$/',
                        'artisan',
                        'CHANGELOG.md',
                        'composer-updater',
                        'README.md',
                    ])
                    ->depth(0)
                    ->ignoreDotFiles(false)
                    ->ignoreUnreadableDirs(false)
                    ->ignoreVCS(true)
                    ->ignoreVCSIgnored(true)
                    ->sortByName()
                    ->files()
            )
    )
    ->setCacheFile(__DIR__.'/.build/php-cs-fixer/.php-cs-fixer-tools.cache')
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setUnsupportedPhpVersionAllowed(true)
    ->setUsingCache(true);
