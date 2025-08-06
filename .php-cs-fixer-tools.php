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

use App\Support\PhpCsFixer\Fixer\AbstractFixer;
use App\Support\PhpCsFixer\Fixer\BladeFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\AbstractCommandLineToolFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\AutocorrectFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\BladeFormatterFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\DockerFmtFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\DotenvLinterFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\LintMdFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\MarkdownLintCli2Fixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\MarkdownLintFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\PintFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\ShfmtFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\SqlFluffFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\TextLintFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\XmlLintFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\YamlFmtFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\ZhLintFixer;
use App\Support\PhpCsFixer\Fixer\InlineHtml\DoctrineSqlFixer;
use App\Support\PhpCsFixer\Fixer\InlineHtml\JsonFixer;
use App\Support\PhpCsFixer\Fixer\InlineHtml\PhpMyAdminSqlFixer;
use App\Support\PhpCsFixer\Fixer\SqlFixer;
use App\Support\PhpCsFixer\Fixers;
use PhpCsFixer\Config;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use Symfony\Component\Finder\Finder;

/**
 * @see https://github.com/laravel/pint/blob/main/resources/presets
 */
return (new Config)
    ->registerCustomFixers($userFixers = iterator_to_array(new Fixers))
    ->registerCustomFixers([
        new BladeFixer,
        new SqlFixer,
    ])
    ->setRules([
        // '@PhpCsFixer:risky' => true,
        'encoding' => true,
        'no_trailing_whitespace' => true,
        'no_whitespace_in_blank_line' => true,
        'non_printable_character' => true,
        'single_blank_line_at_eof' => true,

        // SqlFluffFixer::name() => [
        //     AbstractCommandLineToolFixer::OPTIONS => [
        //         '--dialect' => 'mysql',
        //     ],
        //     SqlFluffFixer::EXTENSIONS => ['sql'],
        // ],

        AutocorrectFixer::name() => true,
        LintMdFixer::name() => true,
        // MarkdownLintCli2Fixer::name() => true,
        MarkdownLintFixer::name() => true,
        // TextLintFixer::name() => true,
        ZhLintFixer::name() => true,

        // PintFixer::name() => true,
        // BladeFormatterFixer::name() => true,

        DockerFmtFixer::name() => true,

        DotenvLinterFixer::name() => true,

        ShfmtFixer::name() => true,

        // DoctrineSqlFixer::name() => true,
        // PhpMyAdminSqlFixer::name() => true,
        SqlFluffFixer::name() => true,

        YamlFmtFixer::name() => true,

        XmlLintFixer::name() => true,

        JsonFixer::name() => true,

        // BladeFixer::name() => [
        //     // BladeFixer::COMMAND => ['node', 'blade-formatter'],
        //     BladeFixer::COMMAND => 'blade-formatter',
        //     BladeFixer::OPTIONS => [
        //         // '--indent-size' => 2,
        //     ],
        // ],
        // SqlFixer::name() => [
        //     SqlFixer::INDENT_STRING => '  ',
        // ],
    ])
    ->setFinder(
        Finder::create()
            ->in(__DIR__)
            ->exclude([
                'Fixtures/',
                'vendor-bin/',
                'vendor/',
            ])
            ->notPath([
                '.chglog/CHANGELOG.tpl.md',
                '/resources\/lang\/.*\.json$/',
                'CHANGELOG.md',
            ])
            ->name(array_unique(array_merge(...array_map(
                fn (AbstractFixer $fixer): array => array_map(
                    static fn (string $extension): string => \sprintf('/\.%s$/', str_replace('.', '\.', $extension)),
                    array_filter(
                        (fn (): array => $this->defaultExtensions())->call($fixer),
                        static fn (string $extension): bool => !\in_array($extension, ['php', 'php'], true)
                    )
                ),
                $userFixers,
            ))))
            ->notName([
                '/\.lock$/',
                // '/\.php$/',
                '/(?<!\.blade)\.php$/',
            ])
            ->ignoreDotFiles(false)
            ->ignoreUnreadableDirs(false)
            ->ignoreVCS(true)
            ->ignoreVCSIgnored(true)
            ->files()
    )
    ->setCacheFile(\sprintf('%s/.build/php-cs-fixer/%s.cache', __DIR__, pathinfo(__FILE__, \PATHINFO_FILENAME)))
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setUnsupportedPhpVersionAllowed(true)
    ->setUsingCache(true);
