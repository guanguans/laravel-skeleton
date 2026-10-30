<?php

/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

use App\Listeners\PrepareRequestListener;
use App\Support\Rector\ClassHandleMethodRector;
use App\Support\Rector\MixinStaticRector;
use Ergebnis\Rector\Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector;
use Guanguans\PhpCsFixerCustomFixers\Support\Utils;
use Guanguans\RectorRules\Rector\Array_\SortListItemOfSameScalarTypeRector;
use Guanguans\RectorRules\Rector\File\AddNoinspectionDocblockToFileFirstStmtRector;
use Guanguans\RectorRules\Rector\FunctionLike\RenameGarbageParamNameRector;
use Guanguans\RectorRules\Rector\Name\RenameToConventionalCaseNameRector;
use Guanguans\RectorRules\Rector\Scalar\ScalarValueToConstFetchRector;
use Guanguans\RectorRules\Set\SetList;
use Pest\Rector\Rules\ChainExpectCallsRector;
use Pest\Rector\Set\PestSetList;
use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodeQuality\Rector\LogicalAnd\LogicalToBooleanRector;
use Rector\CodingStyle\Rector\ArrowFunction\ArrowFunctionDelegatingCallToFirstClassCallableRector;
use Rector\CodingStyle\Rector\Assign\SplitDoubleAssignRector;
use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveEmptyClassMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPublicMethodParameterRector;
use Rector\DeadCode\Rector\StmtsAwareInterface\RemoveDeadInstanceOfAssertRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\Renaming\Rector\ClassConstFetch\RenameClassConstFetchRector;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Transform\Rector\String_\StringToClassConstantRector;
use Rector\Transform\ValueObject\StringToClassConstant;
use Rector\ValueObject\PhpVersion;
use RectorLaravel\Rector\ArrayDimFetch\ArrayToArrGetRector;
use RectorLaravel\Rector\ArrayDimFetch\ServerVariableToRequestFacadeRector;
use RectorLaravel\Rector\Empty_\EmptyToBlankAndFilledFuncRector;
use RectorLaravel\Rector\FuncCall\HelperFuncCallToFacadeClassRector;
use RectorLaravel\Rector\FuncCall\RemoveDumpDataDeadCodeRector;
use RectorLaravel\Rector\FuncCall\TypeHintTappableCallRector;
use RectorLaravel\Rector\FuncCall\UnlinkFuncCallToFileFacadeDeleteRector;
use RectorLaravel\Rector\If_\ThrowIfRector;
use RectorLaravel\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector;
use RectorLaravel\Rector\StaticCall\CarbonToDateFacadeRector;
use RectorLaravel\Rector\StaticCall\DispatchToHelperFunctionsRector;
use RectorLaravel\Rector\StaticCall\RequestStaticValidateToInjectRector;

error_reporting(\E_ALL & ~\E_DEPRECATED & ~\E_USER_DEPRECATED);

return RectorConfig::configure()
    ->withPaths([...Utils::defaultRootDirectories(), ...Utils::defaultRootFiles(), __DIR__.'/bootstrap/app.php'])
    ->withSkip([
        '*.blade.php',
        '*/Fixtures/*',

        __DIR__.'/app/Models/Example.php',
        __DIR__.'/app/Models/Pivots/MorphPivotWithCreatorPivot.php',
        __DIR__.'/app/Models/Pivots/PivotWithCreatorPivot.php',
    ])
    ->withSkip([
        RenameGarbageParamNameRector::class,
        // RenameParamToMatchTypeRector::class,
        // StringToClassConstantRector::class,

        // Skip common rules.
        ChainExpectCallsRector::class,
        LogicalToBooleanRector::class,
        NewlineBetweenClassLikeStmtsRector::class,
        PreferPHPUnitThisCallRector::class,
        SplitDoubleAssignRector::class,
    ])
    ->withSkip([
        ArrayToArrGetRector::class,
        DispatchToHelperFunctionsRector::class,
        EmptyToBlankAndFilledFuncRector::class,
        HelperFuncCallToFacadeClassRector::class,
        RequestStaticValidateToInjectRector::class,
        ThrowIfRector::class,
        ValidationRuleArrayStringValueToArrayRector::class,
    ])
    ->withSkip([
        ArrowFunctionDelegatingCallToFirstClassCallableRector::class => [
            __DIR__.'/app/Support/Mixin/',
            __DIR__.'/app/Support/VarDumper/ServerDumper.php',
        ],
        CarbonToDateFacadeRector::class => [
            __DIR__.'/app/Listeners/TraceEventListener.php',
        ],
        // CompleteDynamicPropertiesRector::class => [
        //     __DIR__.'/app/Support/Mixin/',
        // ],
        RemoveDeadInstanceOfAssertRector::class => [
            __DIR__.'/app/Providers/AppServiceProvider.php',
            __DIR__.'/app/Providers/AutowiredServiceProvider.php',
        ],
        RemoveDumpDataDeadCodeRector::class => [
            __DIR__.'/app/Support/Mixin/SchedulingEventMixin.php',
        ],
        RemoveEmptyClassMethodRector::class => [
            __DIR__.'/app/Observers/UserObserver.php',
        ],
        RemoveExtraParametersRector::class => [
            __DIR__.'/app/Support/Mixin/',
        ],
        RemoveUnusedPublicMethodParameterRector::class => [
            __DIR__.'/app/Listeners/',
            __DIR__.'/app/Observers/UserObserver.php',
        ],
        RenameClassConstFetchRector::class => [
            __DIR__.'/config/database.php',
        ],
        RenameMethodRector::class => [
            __DIR__.'/app/Providers/ViewServiceProvider.php',
        ],
        // RenamePropertyToMatchTypeRector::class => [
        //     __DIR__.'/app/Support/VarDumper/ServerDumper.php',
        // ],
        RenameToConventionalCaseNameRector::class => [
            __DIR__.'/app/Enums/',
            __DIR__.'/app/Models/',
        ],
        ScalarValueToConstFetchRector::class => [
            __DIR__.'/config/database.php',
        ],
        ServerVariableToRequestFacadeRector::class => [
            __DIR__.'/app/Support/VarDumper/ServerDumper.php',
        ],
        SortAssociativeArrayByKeyRector::class => [
            __DIR__.'/app/',
            __DIR__.'/config/',
            __DIR__.'/database/',
            __DIR__.'/routes/',
        ],
        SortListItemOfSameScalarTypeRector::class => [
            __DIR__.'/app/Console/Commands/HealthCheckCommand.php',
            __DIR__.'/app/Console/Commands/InflectorCommand.php',
            __DIR__.'/app/Http/Middleware/VerifySignature.php',
            __DIR__.'/app/Providers/ViewServiceProvider.php',
            __DIR__.'/config/',
            __DIR__.'/tests/Feature/AuthTest.php',
        ],
        StringClassNameToClassConstantRector::class => [
            __DIR__.'/app/Providers/UnlessProductionAggregateServiceProvider.php',
        ],
        // StringToClassConstantRector::class => [
        //     __DIR__.'/app/Listeners/PrepareRequestListener.php',
        //     __FILE__,
        // ],
        TypeHintTappableCallRector::class => [
            __DIR__.'/app/Providers/ValidatorServiceProvider.php',
            __DIR__.'/app/Support/Mixin/QueryBuilder/QueryBuilderMixin.php',
        ],
        UnlinkFuncCallToFileFacadeDeleteRector::class => [
            __DIR__.'/app/Listeners/TraceEventListener.php',
            __DIR__.'/app/Support/StreamWrapper/',
            __DIR__.'/composer-bump',
        ],
    ])
    ->withCache(__DIR__.'/.build/rector/')
    // ->withoutParallel()
    ->withParallel()
    ->withImportNames(importDocBlockNames: false, importShortClasses: false, removeUnusedImports: false)
    // ->withImportNames(true, false, false, false)
    ->reportUnusedSkips()
    ->withFluentCallNewLine()
    ->withTreatClassesAsFinal()
    ->withTypeGuardedClasses([])
    ->withAttributesSets(phpunit: true, all: true)
    ->withComposerBased(phpunit: true, laravel: true)
    ->withPhpVersion(PhpVersion::PHP_85)
    // ->withDowngradeSets(php85: true)
    ->withPhpSets(php85: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        privatization: true,
        // naming: true,
        // namedArgs: true,
        carbon: true,
        rectorPreset: true,
        phpunitCodeQuality: true,
        phpunitNarrowAsserts: true,
        phpunitMockToStub: true,
    )
    ->withSets([
        PestSetList::CODING_STYLE,
        SetList::ALL,
    ])
    ->withRules([
        ClassHandleMethodRector::class,
        MixinStaticRector::class,
    ])
    ->withConfiguredRule(AddNoinspectionDocblockToFileFirstStmtRector::class, [
        '*/app/Support/Mixin/*' => [
            'OverrideMissingInspection',
        ],
        '*/tests/*' => [
            'AnonymousFunctionStaticInspection',
            'NullPointerExceptionInspection',
            'PhpPossiblePolymorphicInvocationInspection',
            'PhpUndefinedClassInspection',
            'PhpUnhandledExceptionInspection',
            'PhpVoidFunctionResultUsedInspection',
            'StaticClosureCanBeUsedInspection',
        ],
    ])
    // ->withConfiguredRule(StringToClassConstantRector::class, [
    //     new StringToClassConstant('X-Request-Id', PrepareRequestListener::class, 'X_REQUEST_ID'),
    // ])
    ->withConfiguredRule(RenameToConventionalCaseNameRector::class, [
        'DB',
        'Debug',
        'MIT',
        'PDO',
        'URL',
        'Value',
        'afterEach',
        'beforeEach',
        'current_password',
        'new_password',
    ]);
