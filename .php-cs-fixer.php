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
use Ergebnis\License\Holder;
use Ergebnis\License\Range;
use Ergebnis\License\Type\MIT;
use Ergebnis\License\Url;
use Ergebnis\License\Year;
use Ergebnis\PhpCsFixer\Config\Factory;
use Ergebnis\PhpCsFixer\Config\Fixers;
use Ergebnis\PhpCsFixer\Config\Rules;
use Ergebnis\PhpCsFixer\Config\RuleSet\Php83;
use PhpCsFixer\Finder;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixerCustomFixers\Fixer\AbstractFixer;

require __DIR__.'/vendor/autoload.php';

return Factory::fromRuleSet(Php83::create()
    ->withHeader(
        (static function (): string {
            $license = MIT::text(
                __DIR__.'/LICENSE',
                Range::since(
                    Year::fromString('2021'),
                    new DateTimeZone('Asia/Shanghai'),
                ),
                Holder::fromString('guanguans<ityaozm@gmail.com>'),
                Url::fromString('https://github.com/guanguans/laravel-skeleton'),
            );

            $license->save();

            return $license->header();
        })()
    )
    ->withCustomFixers(Fixers::fromFixers(...$phpCsFixerCustomFixers = array_filter(
        iterator_to_array(new PhpCsFixerCustomFixers\Fixers),
        static fn (AbstractFixer $fixer): bool => !$fixer instanceof DeprecatedFixerInterface
            && !\array_key_exists($fixer->getName(), Php83::create()->rules()->toArray())
    )))
    // ->withRules(Rules::fromArray(array_reduce(
    //     array_filter($phpCsFixerCustomFixers, static fn (AbstractFixer $fixer): bool => !\in_array(
    //         $fixer->getName(),
    //         [
    //             'PhpCsFixerCustomFixers/*',
    //             'PhpCsFixerCustomFixers/comment_surrounded_by_spaces',
    //             'PhpCsFixerCustomFixers/declare_after_opening_tag',
    //             'PhpCsFixerCustomFixers/isset_to_array_key_exists',
    //             'PhpCsFixerCustomFixers/no_commented_out_code',
    //             'PhpCsFixerCustomFixers/phpdoc_only_allowed_annotations',
    //             'PhpCsFixerCustomFixers/phpdoc_var_annotation_to_assert',
    //         ],
    //         true
    //     )),
    //     static function (array $rules, AbstractFixer $fixer): array {
    //         $rules[$fixer->getName()] = true;
    //
    //         return $rules;
    //     },
    //     []
    // )))
    ->withCustomFixers(Fixers::fromFixers(
        ...$userFixers = iterator_to_array(new App\Support\PhpCsFixer\Fixers)
    ))
    ->withRules(Rules::fromArray(array_reduce(
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
            // SqlFluffFixer::name() => [
            //     AbstractCommandLineToolFixer::OPTIONS => [
            //         '--dialect' => 'mysql',
            //     ],
            //     SqlFluffFixer::EXTENSIONS => ['sql'],
            // ],
        ]
    )))
    ->withRules(Rules::fromArray([
        // '@PHP70Migration' => true,
        // '@PHP70Migration:risky' => true,
        // '@PHP71Migration' => true,
        // '@PHP71Migration:risky' => true,
        // '@PHP73Migration' => true,
        // '@PHP74Migration' => true,
        // '@PHP74Migration:risky' => true,
        // '@PHP80Migration' => true,
        // '@PHP80Migration:risky' => true,
        // '@PHP81Migration' => true,
        // '@PHP82Migration' => true,
        '@PHP83Migration' => true,
        // '@PHP84Migration' => true,
        // '@PHPUnit75Migration:risky' => true,
        // '@PHPUnit84Migration:risky' => true,
        // '@PHPUnit100Migration:risky' => true,
        // '@DoctrineAnnotation' => true,
        // '@PhpCsFixer' => true,
        // '@PhpCsFixer:risky' => true,
    ]))
    ->withRules(Rules::fromArray([
        'PhpCsFixerCustomFixers/phpdoc_tag_no_named_arguments' => false,
        'align_multiline_comment' => [
            'comment_type' => 'phpdocs_only',
        ],
        'attribute_empty_parentheses' => [
            'use_parentheses' => false,
        ],
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                // 'case',
                'continue',
                'declare',
                // 'default',
                'do',
                'exit',
                'for',
                'foreach',
                'goto',
                'if',
                'include',
                'include_once',
                'phpdoc',
                'require',
                'require_once',
                'return',
                'switch',
                'throw',
                'try',
                'while',
                'yield',
                'yield_from',
            ],
        ],
        'class_definition' => [
            'inline_constructor_arguments' => false,
            'multi_line_extends_each_single_line' => false,
            'single_item_single_line' => false,
            'single_line' => false,
            'space_before_parenthesis' => false,
        ],
        'concat_space' => [
            'spacing' => 'none',
        ],
        // 'empty_loop_condition' => [
        //     'style' => 'for',
        // ],
        'explicit_string_variable' => false,
        'final_class' => false,
        'final_internal_class' => false,
        'final_public_method_for_abstract_class' => false,
        'fully_qualified_strict_types' => [
            'import_symbols' => false,
            'leading_backslash_in_global_namespace' => false,
            'phpdoc_tags' => [
                // 'param',
                // 'phpstan-param',
                // 'phpstan-property',
                // 'phpstan-property-read',
                // 'phpstan-property-write',
                // 'phpstan-return',
                // 'phpstan-var',
                // 'property',
                // 'property-read',
                // 'property-write',
                // 'psalm-param',
                // 'psalm-property',
                // 'psalm-property-read',
                // 'psalm-property-write',
                // 'psalm-return',
                // 'psalm-var',
                // 'return',
                // 'see',
                // 'throws',
                // 'var',
            ],
        ],
        'logical_operators' => false,
        'mb_str_functions' => false,
        'native_function_invocation' => [
            'exclude' => [],
            'include' => ['@compiler_optimized', 'is_scalar'],
            'scope' => 'all',
            'strict' => true,
        ],
        'new_with_parentheses' => [
            'anonymous_class' => false,
            'named_class' => false,
        ],
        'no_extra_blank_lines' => [
            'tokens' => [
                'attribute',
                'break',
                'case',
                // 'comma',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'switch',
                'throw',
                'use',
            ],
        ],
        'ordered_traits' => [
            'case_sensitive' => true,
        ],
        'php_unit_data_provider_name' => [
            'prefix' => 'provide',
            'suffix' => 'Cases',
        ],
        'phpdoc_align' => [
            'align' => 'left',
            'spacing' => 1,
            'tags' => [
                'method',
                'param',
                'property',
                'property-read',
                'property-write',
                'return',
                'throws',
                'type',
                'var',
            ],
        ],
        'phpdoc_line_span' => [
            'const' => 'single',
            'method' => 'multi',
            'property' => 'single',
        ],
        'phpdoc_no_alias_tag' => [
            'replacements' => [
                'link' => 'see',
                'type' => 'var',
            ],
        ],
        'phpdoc_order' => [
            'order' => [
                'noinspection',
                'phan-suppress',
                'phpcsSuppress',
                'phpstan-ignore',
                'psalm-suppress',

                'deprecated',
                'internal',
                'covers',
                'uses',
                'dataProvider',
                'param',
                'throws',
                'return',
            ],
        ],
        'phpdoc_order_by_value' => [
            'annotations' => [
                'author',
                'covers',
                'coversNothing',
                'dataProvider',
                'depends',
                'group',
                'internal',
                // 'method',
                'mixin',
                'property',
                'property-read',
                'property-write',
                'requires',
                'throws',
                'uses',
            ],
        ],
        'phpdoc_to_param_type' => [
            'scalar_types' => true,
            'types_map' => [],
            'union_types' => true,
        ],
        'phpdoc_to_property_type' => false,
        'phpdoc_to_return_type' => [
            'scalar_types' => true,
            'types_map' => [],
            'union_types' => true,
        ],
        'simplified_if_return' => true,
        'simplified_null_return' => true,
        'single_line_empty_body' => true,
        'statement_indentation' => [
            'stick_comment_to_next_continuous_control_statement' => true,
        ],
        'static_lambda' => false,
        'static_private_method' => false, // pest
    ])))
    ->setUsingCache(true)
    ->setCacheFile(__DIR__.'/.build/php-cs-fixer/.php-cs-fixer.cache')
    ->setUnsupportedPhpVersionAllowed(true)
    ->setFinder(
        /**
         * @see https://github.com/laravel/pint/blob/main/app/Commands/DefaultCommand.php
         * @see https://github.com/laravel/pint/blob/main/app/Factories/ConfigurationFactory.php
         * @see https://github.com/laravel/pint/blob/main/app/Repositories/ConfigurationJsonRepository.php
         */
        Finder::create()
            ->in([
                __DIR__.'/.github/',
                __DIR__.'/app/',
                __DIR__.'/bootstrap/',
                __DIR__.'/config/',
                __DIR__.'/database/',
                __DIR__.'/public/',
                __DIR__.'/resources/',
                __DIR__.'/routes/',
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
                '/\.php$/',
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
                // '*.blade.php',
            ])
            ->append([
                ...array_filter(
                    glob(__DIR__.'/{*,.*}.*', \GLOB_BRACE),
                    static fn (string $filename): bool => !\in_array($filename, [
                        __DIR__.'/.phpstorm.meta.php',
                        __DIR__.'/_ide_helper.php',
                        __DIR__.'/_ide_helper_actions.php',
                        __DIR__.'/_ide_helper_models.php',
                        __DIR__.'/CHANGELOG.md',
                        __DIR__.'/README.md',
                    ], true)
                ),
                __DIR__.'/artisan',
                __DIR__.'/composer-updater',
            ])
    );
