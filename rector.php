<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use App\Support\Rectors\RenameToPsrNameRector;
use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodeQuality\Rector\ClassMethod\ExplicitReturnNullRector;
use Rector\CodeQuality\Rector\Expression\InlineIfToExplicitIfRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\LogicalAnd\LogicalToBooleanRector;
use Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector;
use Rector\CodingStyle\Rector\Assign\SplitDoubleAssignRector;
use Rector\CodingStyle\Rector\ClassMethod\MakeInheritedMethodVisibilitySameAsParentRector;
use Rector\CodingStyle\Rector\Closure\StaticClosureRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector;
use Rector\DeadCode\Rector\ConstFetch\RemovePhpVersionIdCheckRector;
use Rector\DeadCode\Rector\If_\RemoveAlwaysTrueIfConditionRector;
use Rector\DeadCode\Rector\If_\UnwrapFutureCompatibleIfPhpVersionRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\Php80\Rector\Catch_\RemoveUnusedVariableInCatchRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\Rector\StaticCall\RenameStaticMethodRector;
use Rector\Renaming\Rector\String_\RenameStringRector;
use Rector\Set\ValueObject\DowngradeLevelSetList;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/*.php',
        __DIR__.'/.*.php',
        __DIR__.'/app',
        __DIR__.'/composer-updater',
        __DIR__.'/config',
        __DIR__.'/routes',
    ])
    ->withParallel()
    // ->withoutParallel()
    ->withImportNames(false)
    // ->withAttributesSets()
    // ->withDeadCodeLevel(42)
    ->withTypeCoverageLevel(3)
    // ->withFluentCallNewLine()
    ->withPhpSets(php81: true)
    ->withPreparedSets(deadCode: true, codeQuality: true, codingStyle: true, instanceOf: true)
    ->withSets([
        DowngradeLevelSetList::DOWN_TO_PHP_81,
        LaravelSetList::LARAVEL_100,
        // LaravelSetList::LARAVEL_STATIC_TO_INJECTION,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
        LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES,
        LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
        LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,
    ])
    ->withRules([
    ])
    ->withConfiguredRule(RenameFunctionRector::class, [
        'test' => 'it',
    ])
    ->withConfiguredRule(RenameToPsrNameRector::class, [
        '_*',
    ])
    ->withConfiguredRule(RenameClassRector::class, [
    ])
    ->withConfiguredRule(RenameStaticMethodRector::class, [
    ])
    ->withConfiguredRule(RenameStringRector::class, [
    ])
    ->withSkip([
        '**/__snapshots__/*',
        '**/Fixtures/*',
        __DIR__.'/.phpstorm.meta.php',
        __DIR__.'/_ide_helper.php',
        __DIR__.'/_ide_helper_models.php',
        __DIR__.'/app/Console/Commands/ParsePHPFileToASTCommand.php',
        __DIR__.'/app/Support/Http',
        __DIR__.'/dcat_admin_ide_helper.php',
        __DIR__.'/deploy.example.php',
        __DIR__.'/deploy.php',
        __FILE__,
    ])
    ->withSkip([
        CompleteDynamicPropertiesRector::class,
        DisallowedEmptyRuleFixerRector::class,
        EncapsedStringsToSprintfRector::class,
        ExplicitBoolCompareRector::class,
        ExplicitReturnNullRector::class,
        InlineIfToExplicitIfRector::class,
        JsonThrowOnErrorRector::class,
        LogicalToBooleanRector::class,
        NullToStrictStringFuncCallArgRector::class,
        PostIncDecToPreIncDecRector::class,
        RemoveExtraParametersRector::class,
        RemoveUnusedPrivateMethodRector::class,
        SplitDoubleAssignRector::class,
        WrapEncapsedVariableInCurlyBracesRector::class,
    ])
    ->withSkip([
        MakeInheritedMethodVisibilitySameAsParentRector::class => [
            __DIR__.'/app/Admin/Actions/Show',
        ],
        RemoveUnusedVariableInCatchRector::class => [
            __DIR__.'/app/Support/Macros/CommandMacro.php',
        ],
        RemovePhpVersionIdCheckRector::class => [
            __DIR__.'/app/Console/Commands/HealthCheckCommand.php',
        ],
        UnwrapFutureCompatibleIfPhpVersionRector::class => [
            __DIR__.'/app/Console/Commands/HealthCheckCommand.php',
        ],
        RemoveAlwaysTrueIfConditionRector::class => [
            __DIR__.'/app/Support/Discover.php',
        ],
        StaticArrowFunctionRector::class => $staticClosureSkipPaths = [
            __DIR__.'/app/Support/helpers.php',
            __DIR__.'/app/Admin/Controllers',
            __DIR__.'/app/Admin/Forms',
            __DIR__.'/tests',
        ],
        StaticClosureRector::class => $staticClosureSkipPaths,
    ]);
