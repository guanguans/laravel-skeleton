<?php

/** @noinspection PhpInternalEntityUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpUnusedAliasInspection */

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
use Ergebnis\Rector\Rules\Expressions\Arrays\SortAssociativeArrayByKeyRector;
use Ergebnis\Rector\Rules\Faker\GeneratorPropertyFetchToMethodCallRector;
use Ergebnis\Rector\Rules\Files\ReferenceNamespacedSymbolsRelativeToNamespacePrefixRector;
use Guanguans\RectorRules\Rector\ClassMethod\PrivateToProtectedVisibilityForTraitRector;
use Guanguans\RectorRules\Rector\File\AddNoinspectionDocblockToFileFirstStmtRector;
use Guanguans\RectorRules\Rector\FunctionLike\RenameGarbageParamNameRector;
use Guanguans\RectorRules\Rector\Name\RenameToConventionalCaseNameRector;
use Guanguans\RectorRules\Set\SetList;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\LogicalAnd\LogicalToBooleanRector;
use Rector\CodingStyle\Rector\ArrowFunction\ArrowFunctionDelegatingCallToFirstClassCallableRector;
use Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector;
use Rector\CodingStyle\Rector\ClassLike\NewlineBetweenClassLikeStmtsRector;
use Rector\CodingStyle\Rector\Closure\StaticClosureRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\CodingStyle\Rector\Enum_\EnumCaseToPascalCaseRector;
use Rector\CodingStyle\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveEmptyClassMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPublicMethodParameterRector;
use Rector\EarlyReturn\Rector\If_\ChangeOrIfContinueToMultiContinueRector;
use Rector\EarlyReturn\Rector\Return_\ReturnBinaryOrToEarlyReturnRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\Php82\Rector\Param\AddSensitiveParameterAttributeRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\Transform\Rector\Scalar\ScalarValueToConstFetchRector;
use Rector\Transform\Rector\String_\StringToClassConstantRector;
use Rector\Transform\ValueObject\StringToClassConstant;
use Rector\ValueObject\PhpVersion;
use RectorLaravel\Rector\ArrayDimFetch\ArrayToArrGetRector;
use RectorLaravel\Rector\ArrayDimFetch\ServerVariableToRequestFacadeRector;
use RectorLaravel\Rector\Class_\FillablePropertyToFillableAttributeRector;
use RectorLaravel\Rector\Class_\HiddenPropertyToHiddenAttributeRector;
use RectorLaravel\Rector\Class_\TablePropertyToTableAttributeRector;
use RectorLaravel\Rector\Empty_\EmptyToBlankAndFilledFuncRector;
use RectorLaravel\Rector\FuncCall\HelperFuncCallToFacadeClassRector;
use RectorLaravel\Rector\FuncCall\RemoveDumpDataDeadCodeRector;
use RectorLaravel\Rector\FuncCall\TypeHintTappableCallRector;
use RectorLaravel\Rector\If_\ThrowIfRector;
use RectorLaravel\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector;
use RectorLaravel\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector;
use RectorLaravel\Rector\StaticCall\DispatchToHelperFunctionsRector;
use RectorLaravel\Set\LaravelSetProvider;
use RectorPest\Rules\ChainExpectCallsRector;
use RectorPest\Set\PestLevelSetList;
use RectorPest\Set\PestSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app/',
        // __DIR__.'/bootstrap/',
        __DIR__.'/bootstrap/app.php',
        // __DIR__.'/config/',
        __DIR__.'/database/',
        // __DIR__.'/public/',
        // __DIR__.'/resources/',
        __DIR__.'/routes/',
        __DIR__.'/tests/',
        __DIR__.'/artisan',
        __DIR__.'/composer-bump',
    ])
    ->withRootFiles()
    ->withSkip([
        '*.blade.php',
        '*/Fixtures/*',
        __DIR__.'/_ide_helper_.php',
        __DIR__.'/app/Listeners/TraceEventListener.php',
        __DIR__.'/app/Providers/UnlessProductionAggregateServiceProvider.php',
    ])
    ->withCache(__DIR__.'/.build/rector/')
    // ->withoutParallel()
    ->withParallel()
    ->withImportNames(importDocBlockNames: false, importShortClasses: false)
    // ->withImportNames(importNames: false)
    // ->withEditorUrl()
    ->withFluentCallNewLine()
    ->withTreatClassesAsFinal()
    ->withAttributesSets(phpunit: true, all: true)
    ->withComposerBased(phpunit: true, laravel: true)
    ->withSetProviders(LaravelSetProvider::class)
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
        naming: true,
        instanceOf: true,
        earlyReturn: true,
        // strictBooleans: true,
        carbon: true,
        rectorPreset: true,
        phpunitCodeQuality: true,
    )
    ->withSets([
        SetList::ALL,
        PestLevelSetList::UP_TO_PEST_40,
        PestSetList::PEST_CHAIN,
        PestSetList::PEST_CODE_QUALITY,
        PestSetList::PEST_LARAVEL,
        PestSetList::PEST_MIGRATION,
    ])
    ->withRules([
        ArraySpreadInsteadOfArrayMergeRector::class,
        ClassHandleMethodRector::class,
        EnumCaseToPascalCaseRector::class,
        GeneratorPropertyFetchToMethodCallRector::class,
        JsonThrowOnErrorRector::class,
        SortAssociativeArrayByKeyRector::class,
        StaticArrowFunctionRector::class,
        StaticClosureRector::class,
    ])
    ->withConfiguredRule(AddNoinspectionDocblockToFileFirstStmtRector::class, [
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
    ->registerDecoratingNodeVisitor(ParentConnectingVisitor::class)
    ->withConfiguredRule(RenameToConventionalCaseNameRector::class, [
        'afterEach',
        'beforeEach',
        'current_password',
        'DB',
        'Debug',
        'MIT',
        'new_password',
        'PDO',
        'URL',
        'Value',
    ])
    ->withConfiguredRule(ReferenceNamespacedSymbolsRelativeToNamespacePrefixRector::class, [
        // 'namespacePrefixes' => ['App'],
    ])
    ->withConfiguredRule(StringToClassConstantRector::class, [
        new StringToClassConstant('X-Request-Id', PrepareRequestListener::class, 'X_REQUEST_ID'),
    ])
    ->withSkip([
        AddSensitiveParameterAttributeRector::class,
        ChainExpectCallsRector::class,
        PrivateToProtectedVisibilityForTraitRector::class,
        RenameGarbageParamNameRector::class,
        RenameParamToMatchTypeRector::class,
        ScalarValueToConstFetchRector::class,
        StringToClassConstantRector::class,

        ChangeOrIfContinueToMultiContinueRector::class,
        DisallowedEmptyRuleFixerRector::class,
        EncapsedStringsToSprintfRector::class,
        ExplicitBoolCompareRector::class,
        LogicalToBooleanRector::class,
        NewlineBetweenClassLikeStmtsRector::class,
        PreferPHPUnitThisCallRector::class,
        ReturnBinaryOrToEarlyReturnRector::class,
        WrapEncapsedVariableInCurlyBracesRector::class,
    ])
    ->withSkip([
        ContainerBindConcreteWithClosureOnlyRector::class,
        FillablePropertyToFillableAttributeRector::class,
        HiddenPropertyToHiddenAttributeRector::class,
        TablePropertyToTableAttributeRector::class,
        ValidationRuleArrayStringValueToArrayRector::class,

        ArrayToArrGetRector::class,
        DispatchToHelperFunctionsRector::class,
        EmptyToBlankAndFilledFuncRector::class,
        HelperFuncCallToFacadeClassRector::class,
        ThrowIfRector::class,
    ])
    ->withSkip([
        ArrowFunctionDelegatingCallToFirstClassCallableRector::class => [
            __DIR__.'/app/Support/Mixin/',
            __DIR__.'/app/Support/VarDumper/ServerDumper.php',
        ],
        CompleteDynamicPropertiesRector::class => $mixinsPath = [
            __DIR__.'/app/Support/Client/AbstractClient.php',
            __DIR__.'/app/Support/Mixin/',
        ],
        JsonThrowOnErrorRector::class => [
            __DIR__.'/app/Support/helpers.php',
            __DIR__.'/app/Support/Mixin/CollectionMixin.php',
            __DIR__.'/app/Support/Sse/ServerSentEvent.php',
            __DIR__.'/tests/Pest.php',
        ],
        RemoveDumpDataDeadCodeRector::class => [
            __DIR__.'/app/Support/Mixin/SchedulingEventMixin.php',
        ],
        RemoveEmptyClassMethodRector::class => [
            __DIR__.'/app/Observers/UserObserver.php',
        ],
        RemoveExtraParametersRector::class => $mixinsPath,
        RemoveUnusedPublicMethodParameterRector::class => [
            __DIR__.'/app/Listeners/',
            __DIR__.'/app/Observers/UserObserver.php',
        ],
        RenamePropertyToMatchTypeRector::class => [
            __DIR__.'/app/Support/VarDumper/ServerDumper.php',
        ],
        RenameToConventionalCaseNameRector::class => [
            __DIR__.'/app/Enums/',
            __DIR__.'/app/Models/',
        ],
        ServerVariableToRequestFacadeRector::class => [
            __DIR__.'/app/Listeners/RunCommandInDebugModeListener.php',
            __DIR__.'/app/Support/VarDumper/ServerDumper.php',
        ],
        SortAssociativeArrayByKeyRector::class => [
            __DIR__.'/app/',
            __DIR__.'/config/',
            __DIR__.'/database/',
            __DIR__.'/routes/',
            __DIR__.'/tests/',
        ],
        StaticArrowFunctionRector::class => $staticClosureSkipPaths = [
            __DIR__.'/tests/*Test.php',
            __DIR__.'/tests/Pest.php',
        ],
        StaticClosureRector::class => $staticClosureSkipPaths,
        TypeHintTappableCallRector::class => [
            __DIR__.'/app/Support/Mixin/QueryBuilder/QueryBuilderMixin.php',
            __DIR__.'/app/Providers/ValidatorServiceProvider.php',
        ],
    ]);
