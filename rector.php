<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use App\Support\Rectors\RenameToPsrNameRector;
use App\Support\Traits\Cacheable;
use Composer\Autoload\ClassLoader;
use Ergebnis\Rector\Rules\Arrays\SortAssociativeArrayByKeyRector;
use Illuminate\Support\Collection;
use Rector\Carbon\Rector\FuncCall\TimeFuncCallToCarbonRector;
use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodeQuality\Rector\ClassMethod\ExplicitReturnNullRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\LogicalAnd\LogicalToBooleanRector;
use Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector;
use Rector\CodingStyle\Rector\Closure\StaticClosureRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveEmptyClassMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ConstFetch\RemovePhpVersionIdCheckRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php80\Rector\Catch_\RemoveUnusedVariableInCatchRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\Rector\StaticCall\RenameStaticMethodRector;
use Rector\Renaming\Rector\String_\RenameStringRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\Transform\Rector\ClassMethod\ReturnTypeWillChangeRector;
use Rector\Transform\Rector\FileWithoutNamespace\RectorConfigBuilderRector;
use Rector\Transform\Rector\FuncCall\FuncCallToStaticCallRector;
use Rector\Transform\ValueObject\ClassMethodReference;
use Rector\Transform\ValueObject\FuncCallToStaticCall;
use Rector\ValueObject\PhpVersion;
use Rector\ValueObject\Visibility;
use Rector\Visibility\Rector\ClassMethod\ChangeMethodVisibilityRector;
use Rector\Visibility\ValueObject\ChangeMethodVisibility;
use RectorLaravel\Rector\ArrayDimFetch\EnvVariableToEnvHelperRector;
use RectorLaravel\Rector\Empty_\EmptyToBlankAndFilledFuncRector;
use RectorLaravel\Rector\FuncCall\HelperFuncCallToFacadeClassRector;
use RectorLaravel\Rector\FuncCall\RemoveDumpDataDeadCodeRector;
use RectorLaravel\Rector\FuncCall\TypeHintTappableCallRector;
use RectorLaravel\Rector\StaticCall\DispatchToHelperFunctionsRector;
use RectorLaravel\Set\LaravelSetList;

/** @noinspection PhpUnhandledExceptionInspection */
return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/composer-updater',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/routes',
        __DIR__.'/tests',
        ...glob(__DIR__.'/{*,.*}.php', \GLOB_BRACE),
        __DIR__.'/composer-updater',
    ])
    ->withRootFiles()
    // ->withSkipPath(__DIR__.'/tests.php')
    ->withSkip([
        '**/__snapshots__/*',
        '**/Fixtures/*',
        __DIR__.'/.phpstorm.meta.php',
        __DIR__.'/_ide_helper*.php',
        __DIR__.'/_ide_helper.php',
        __DIR__.'/_ide_helper_models.php',
        __DIR__.'/app/Console/Commands/FindDumpStatementCommand.php',
        __DIR__.'/app/Console/Commands/ParsePHPFileToASTCommand.php',
        __DIR__.'/app/Support/Http',
        __DIR__.'/database/migrations/2025_03_02_213337_create_settings_table.php',
        __DIR__.'/dcat_admin_ide_helper.php',
        __DIR__.'/deploy.example.php',
        __DIR__.'/deploy.php',
    ])
    ->withCache(__DIR__.'/.build/rector/')
    ->withParallel()
    // ->withoutParallel()
    ->withImportNames(importNames: false)
    // ->withImportNames(importDocBlockNames: false, importShortClasses: false)
    ->withFluentCallNewLine()
    ->withAttributesSets(phpunit: true)
    // ->withComposerBased(phpunit: true)
    ->withPhpVersion(PhpVersion::PHP_83)
    // ->withDowngradeSets(php83: true)
    ->withPhpSets(php83: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        // naming: true,
        instanceOf: true,
        // earlyReturn: true,
        carbon: true,
        phpunitCodeQuality: true,
        phpunit: true,
    )
    ->withSets([
        PHPUnitSetList::PHPUNIT_100,
        LaravelSetList::LARAVEL_110,
        ...collect((new ReflectionClass(LaravelSetList::class))->getConstants(ReflectionClassConstant::IS_PUBLIC))
            ->reject(
                static fn (
                    string $constant,
                    string $name
                ): bool => in_array($name, ['LARAVEL_STATIC_TO_INJECTION', 'LARAVEL_'], true)
                    || preg_match('/^LARAVEL_\d{2,3}$/', $name)
            )
            // ->dd()
            ->values()
            ->all(),
    ])
    ->withRules([
        AddOverrideAttributeToOverriddenMethodsRector::class,
        RectorConfigBuilderRector::class,
        SortAssociativeArrayByKeyRector::class,
        StaticArrowFunctionRector::class,
        StaticClosureRector::class,
        ...collect(spl_autoload_functions())
            ->pipe(static fn (Collection $splAutoloadFunctions): Collection => collect(
                $splAutoloadFunctions
                    ->firstOrFail(
                        static fn (mixed $loader): bool => is_array($loader) && $loader[0] instanceof ClassLoader
                    )[0]
                    ->getClassMap()
            ))
            ->keys()
            ->filter(static fn (string $class): bool => str_starts_with($class, 'RectorLaravel\Rector'))
            ->filter(static fn (string $class): bool => (new ReflectionClass($class))->isInstantiable())
            // ->filter(static fn (string $class): bool => is_subclass_of($class, ConfigurableRectorInterface::class))
            ->values()
            // ->dd()
            ->all(),
    ])
    // ->withConfiguredRule(FuncCallToStaticCallRector::class, [
    //     new FuncCallToStaticCall('request', Illuminate\Support\Facades\Request::class, 'getFacadeRoot'),
    // ])
    ->withConfiguredRule(ReturnTypeWillChangeRector::class, [
        new ClassMethodReference(ArrayAccess::class, 'offsetGet'),
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
    ->withConfiguredRule(ChangeMethodVisibilityRector::class, [
        new ChangeMethodVisibility(Cacheable::class, 'getCacheExpiresTime', Visibility::PRIVATE),
    ])

    ->withSkip([
        CompleteDynamicPropertiesRector::class,
        DisallowedEmptyRuleFixerRector::class,
        EncapsedStringsToSprintfRector::class,
        ExplicitBoolCompareRector::class,
        ExplicitReturnNullRector::class,
        LogicalToBooleanRector::class,
        NullToStrictStringFuncCallArgRector::class,
        RemoveExtraParametersRector::class,
        WrapEncapsedVariableInCurlyBracesRector::class,
    ])
    ->withSkip([
        DispatchToHelperFunctionsRector::class,
        EmptyToBlankAndFilledFuncRector::class,
        HelperFuncCallToFacadeClassRector::class,
        TypeHintTappableCallRector::class,
    ])
    ->withSkip([
        EnvVariableToEnvHelperRector::class => [
            __DIR__.'/app/Providers/AppServiceProvider.php',
        ],
        TimeFuncCallToCarbonRector::class => [
            __DIR__.'/app/Support/StreamWrappers',
        ],
        RemoveEmptyClassMethodRector::class => [
            __DIR__.'/app/Support/StreamWrappers',
        ],
        RemoveUnusedVariableInCatchRector::class => [
            __DIR__.'/app/Support/Mixins/CommandMixin.php',
        ],
        RemovePhpVersionIdCheckRector::class => [
            __DIR__.'/app/Console/Commands/HealthCheckCommand.php',
        ],
        RemoveUselessParamTagRector::class => [
            __DIR__.'/app/Models/Concerns/SerializeDate.php',
        ],
        RemoveDumpDataDeadCodeRector::class => [
            __DIR__.'/app/Console/Commands/ShowUnsupportedRequiresCommand.php',
            __DIR__.'/routes/console.php',
            __DIR__.'/tests.php',
        ],
        StaticArrowFunctionRector::class => $staticClosureSkipPaths = [
            __DIR__.'/app/Admin/Controllers',
            __DIR__.'/app/Admin/Forms',
            __DIR__.'/app/Support/helpers.php',
            __DIR__.'/routes/console.php',
            __DIR__.'/tests',
        ],
        StaticClosureRector::class => $staticClosureSkipPaths,
        SortAssociativeArrayByKeyRector::class => [
            __DIR__.'/app',
            __DIR__.'/composer-updater',
            __DIR__.'/config',
            __DIR__.'/database',
            __DIR__.'/routes',
            __DIR__.'/tests',
        ],
    ]);
