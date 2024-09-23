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
use App\Support\Traits\Cacheable;
use Rector\Carbon\Rector\FuncCall\DateFuncCallToCarbonRector;
use Rector\Carbon\Rector\FuncCall\TimeFuncCallToCarbonRector;
use Rector\Carbon\Rector\MethodCall\DateTimeMethodCallToCarbonRector;
use Rector\Carbon\Rector\New_\DateTimeInstanceToCarbonRector;
use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodeQuality\Rector\ClassMethod\ExplicitReturnNullRector;
use Rector\CodeQuality\Rector\Expression\InlineIfToExplicitIfRector;
use Rector\CodeQuality\Rector\FuncCall\CompactToVariablesRector;
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
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\Rector\StaticCall\RenameStaticMethodRector;
use Rector\Renaming\Rector\String_\RenameStringRector;
use Rector\Set\ValueObject\DowngradeLevelSetList;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\Transform\Rector\ClassMethod\ReturnTypeWillChangeRector;
use Rector\Transform\Rector\FileWithoutNamespace\RectorConfigBuilderRector;
use Rector\Transform\ValueObject\ClassMethodReference;
use Rector\ValueObject\Visibility;
use Rector\Visibility\Rector\ClassMethod\ChangeMethodVisibilityRector;
use Rector\Visibility\ValueObject\ChangeMethodVisibility;
use RectorLaravel\Rector\FuncCall\RemoveDumpDataDeadCodeRector;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/*.php',
        __DIR__.'/.*.php',
        __DIR__.'/app',
        __DIR__.'/composer-updater',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->withParallel()
    // ->withoutParallel()
    ->withImportNames(importNames: false, importDocBlockNames: false)
    // ->withAttributesSets()
    // ->withDeadCodeLevel(42)
    ->withTypeCoverageLevel(23)
    // ->withFluentCallNewLine()
    ->withPhpSets(php82: true)
    ->withPreparedSets(deadCode: true, codeQuality: true, codingStyle: true, instanceOf: true)
    ->withSets([
        DowngradeLevelSetList::DOWN_TO_PHP_82,
        LaravelSetList::LARAVEL_110,
        // LaravelSetList::LARAVEL_STATIC_TO_INJECTION,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
        LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES,
        LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
        LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,
    ])
    ->withRules([
        AddOverrideAttributeToOverriddenMethodsRector::class,
        RectorConfigBuilderRector::class,
        StaticArrowFunctionRector::class,
        StaticClosureRector::class,

        DateFuncCallToCarbonRector::class,
        DateTimeInstanceToCarbonRector::class,
        DateTimeMethodCallToCarbonRector::class,
        TimeFuncCallToCarbonRector::class,
    ])
    ->withRules([
        // // RectorLaravel\Rector\Assign\CallOnAppArrayAccessToStandaloneAssignRector::class,
        // // RectorLaravel\Rector\Cast\DatabaseExpressionCastsToMethodCallRector::class,
        RectorLaravel\Rector\ClassMethod\AddParentBootToModelClassMethodRector::class,
        RectorLaravel\Rector\ClassMethod\AddParentRegisterToEventServiceProviderRector::class,
        RectorLaravel\Rector\ClassMethod\MigrateToSimplifiedAttributeRector::class,
        RectorLaravel\Rector\Class_\AddExtendsAnnotationToModelFactoriesRector::class,
        RectorLaravel\Rector\Class_\AddMockConsoleOutputFalseToConsoleTestsRector::class,
        RectorLaravel\Rector\Class_\AnonymousMigrationsRector::class,
        RectorLaravel\Rector\Class_\ModelCastsPropertyToCastsMethodRector::class,
        RectorLaravel\Rector\Class_\PropertyDeferToDeferrableProviderToRector::class,
        RectorLaravel\Rector\Class_\RemoveModelPropertyFromFactoriesRector::class,
        // // RectorLaravel\Rector\Class_\ReplaceExpectsMethodsInTestsRector::class,
        // // RectorLaravel\Rector\Class_\UnifyModelDatesWithCastsRector::class,
        // RectorLaravel\Rector\Empty_\EmptyToBlankAndFilledFuncRector::class,
        RectorLaravel\Rector\Expr\AppEnvironmentComparisonToParameterRector::class,
        RectorLaravel\Rector\Expr\SubStrToStartsWithOrEndsWithStaticMethodCallRector\SubStrToStartsWithOrEndsWithStaticMethodCallRector::class,
        // // RectorLaravel\Rector\FuncCall\DispatchNonShouldQueueToDispatchSyncRector::class,
        // // RectorLaravel\Rector\FuncCall\FactoryFuncCallToStaticCallRector::class,
        // RectorLaravel\Rector\FuncCall\HelperFuncCallToFacadeClassRector::class,
        RectorLaravel\Rector\FuncCall\NotFilledBlankFuncCallToBlankFilledFuncCallRector::class,
        RectorLaravel\Rector\FuncCall\NowFuncWithStartOfDayMethodCallToTodayFuncRector::class,
        RemoveDumpDataDeadCodeRector::class,
        RectorLaravel\Rector\FuncCall\RemoveRedundantValueCallsRector::class,
        RectorLaravel\Rector\FuncCall\RemoveRedundantWithCallsRector::class,
        RectorLaravel\Rector\FuncCall\SleepFuncToSleepStaticCallRector::class,
        // RectorLaravel\Rector\FuncCall\ThrowIfAndThrowUnlessExceptionsToUseClassStringRector::class,
        RectorLaravel\Rector\If_\AbortIfRector::class,
        RectorLaravel\Rector\If_\ReportIfRector::class,
        // RectorLaravel\Rector\If_\ThrowIfRector::class,
        RectorLaravel\Rector\MethodCall\AssertStatusToAssertMethodRector::class,
        RectorLaravel\Rector\MethodCall\ChangeQueryWhereDateValueWithCarbonRector::class,
        // // RectorLaravel\Rector\MethodCall\DatabaseExpressionToStringToMethodCallRector::class,
        RectorLaravel\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector::class,
        RectorLaravel\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector::class,
        // // RectorLaravel\Rector\MethodCall\FactoryApplyingStatesRector::class,
        RectorLaravel\Rector\MethodCall\JsonCallToExplicitJsonCallRector::class,
        // RectorLaravel\Rector\MethodCall\LumenRoutesStringActionToUsesArrayRector::class,
        // RectorLaravel\Rector\MethodCall\LumenRoutesStringMiddlewareToArrayRector::class,
        RectorLaravel\Rector\MethodCall\RedirectBackToBackHelperRector::class,
        RectorLaravel\Rector\MethodCall\RedirectRouteToToRouteHelperRector::class,
        RectorLaravel\Rector\MethodCall\RefactorBlueprintGeometryColumnsRector::class,
        // // RectorLaravel\Rector\MethodCall\ReplaceWithoutJobsEventsAndNotificationsWithFacadeFakeRector::class,
        RectorLaravel\Rector\MethodCall\UseComponentPropertyWithinCommandsRector::class,
        RectorLaravel\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector::class,
        // // RectorLaravel\Rector\Namespace_\FactoryDefinitionRector::class,
        RectorLaravel\Rector\New_\AddGuardToLoginEventRector::class,
        RectorLaravel\Rector\PropertyFetch\ReplaceFakerInstanceWithHelperRector::class,
        RectorLaravel\Rector\StaticCall\DispatchToHelperFunctionsRector::class,
        // RectorLaravel\Rector\StaticCall\MinutesToSecondsInCacheRector::class,
        RectorLaravel\Rector\StaticCall\Redirect301ToPermanentRedirectRector::class,
        // // RectorLaravel\Rector\StaticCall\ReplaceAssertTimesSendWithAssertSentTimesRector::class,
    ])
    ->withConfiguredRule(ReturnTypeWillChangeRector::class, [
        new ClassMethodReference(ArrayAccess::class, 'offsetGet'),
    ])
    ->withRules([
        // // RectorLaravel\Rector\ClassMethod\AddArgumentDefaultValueRector::class,
        // // RectorLaravel\Rector\FuncCall\ArgumentFuncCallToMethodCallRector::class,
        // RectorLaravel\Rector\MethodCall\EloquentOrderByToLatestOrOldestRector::class,
        // RectorLaravel\Rector\MethodCall\ReplaceServiceContainerCallArgRector::class,
        // RectorLaravel\Rector\PropertyFetch\OptionalToNullsafeOperatorRector::class,
        // // RectorLaravel\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector::class,
        // RectorLaravel\Rector\StaticCall\RouteActionCallableRector::class,
    ])
    ->withConfiguredRule(RectorLaravel\Rector\MethodCall\EloquentOrderByToLatestOrOldestRector::class, [
    ])
    ->withConfiguredRule(RectorLaravel\Rector\MethodCall\ReplaceServiceContainerCallArgRector::class, [
    ])
    ->withConfiguredRule(RectorLaravel\Rector\PropertyFetch\OptionalToNullsafeOperatorRector::class, [
    ])
    ->withConfiguredRule(RectorLaravel\Rector\StaticCall\RouteActionCallableRector::class, [
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
        '**/__snapshots__/*',
        '**/Fixtures/*',
        __DIR__.'/.phpstorm.meta.php',
        __DIR__.'/_ide_helper*.php',
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
        // JsonThrowOnErrorRector::class,
        LogicalToBooleanRector::class,
        NullToStrictStringFuncCallArgRector::class,
        // PostIncDecToPreIncDecRector::class,
        RemoveExtraParametersRector::class,
        RemoveUnusedPrivateMethodRector::class,
        SplitDoubleAssignRector::class,
        WrapEncapsedVariableInCurlyBracesRector::class,
    ])
    ->withSkip([
        CompactToVariablesRector::class => [
            __DIR__.'/app/Support/ApiResponse',
        ],
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
        RemoveDumpDataDeadCodeRector::class => [
            __DIR__.'/app/Console/Commands/ShowUnsupportedRequiresCommand.php',
        ],
        StaticArrowFunctionRector::class => $staticClosureSkipPaths = [
            __DIR__.'/app/Support/helpers.php',
            __DIR__.'/app/Admin/Controllers',
            __DIR__.'/app/Admin/Forms',
            __DIR__.'/tests',
        ],
        StaticClosureRector::class => $staticClosureSkipPaths,
    ]);
