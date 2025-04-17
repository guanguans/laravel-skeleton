<?php

/** @noinspection PhpInternalEntityUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */
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

use App\Support\Rectors\ClassHandleMethodRector;
use Carbon\Carbon;
use Ergebnis\Rector\Rules\Arrays\SortAssociativeArrayByKeyRector;
use Guanguans\MonorepoBuilderWorker\Support\Rectors\RenameToPsrNameRector;
use Illuminate\Support\Carbon as IlluminateCarbon;
use Illuminate\Support\Str;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\LogicalAnd\LogicalToBooleanRector;
use Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector;
use Rector\CodingStyle\Rector\Closure\StaticClosureRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\CodingStyle\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassLike\RemoveAnnotationRector;
use Rector\EarlyReturn\Rector\If_\ChangeOrIfContinueToMultiContinueRector;
use Rector\EarlyReturn\Rector\Return_\ReturnBinaryOrToEarlyReturnRector;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameVariableToMatchNewTypeRector;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchExprVariableRector;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchMethodCallReturnTypeRector;
use Rector\NodeTypeResolver\PHPStan\Scope\Contract\NodeVisitor\ScopeResolverNodeVisitorInterface;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\Php74\Rector\Ternary\ParenthesizeNestedTernaryRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\Transform\Rector\ClassMethod\ReturnTypeWillChangeRector;
use Rector\Transform\Rector\FuncCall\FuncCallToStaticCallRector;
use Rector\Transform\Rector\StaticCall\StaticCallToFuncCallRector;
use Rector\Transform\ValueObject\ClassMethodReference;
use Rector\Transform\ValueObject\FuncCallToStaticCall;
use Rector\Transform\ValueObject\StaticCallToFuncCall;
use Rector\ValueObject\PhpVersion;
use RectorLaravel\Rector\ArrayDimFetch\EnvVariableToEnvHelperRector;
use RectorLaravel\Rector\ArrayDimFetch\ServerVariableToRequestFacadeRector;
use RectorLaravel\Rector\Class_\ModelCastsPropertyToCastsMethodRector;
use RectorLaravel\Rector\Empty_\EmptyToBlankAndFilledFuncRector;
use RectorLaravel\Rector\FuncCall\HelperFuncCallToFacadeClassRector;
use RectorLaravel\Rector\FuncCall\TypeHintTappableCallRector;
use RectorLaravel\Rector\StaticCall\DispatchToHelperFunctionsRector;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app/View/',
        // __DIR__.'/app/',
        // __DIR__.'/bootstrap/',
        // __DIR__.'/config/',
        // __DIR__.'/database/',
        // __DIR__.'/public/',
        // __DIR__.'/resources/',
        // __DIR__.'/routes/',
        // __DIR__.'/tests/',
        ...array_filter(
            glob(__DIR__.'/{*,.*}.php', \GLOB_BRACE),
            static fn (string $filename): bool => !\in_array($filename, [
                __DIR__.'/.phpstorm.meta.php',
                __DIR__.'/_ide_helper.php',
                __DIR__.'/_ide_helper_models.php',
            ], true)
        ),
        __DIR__.'/artisan',
        __DIR__.'/composer-updater',
    ])
    ->withRootFiles()
    // ->withSkipPath(__DIR__.'/tests.php')
    ->withSkip([
        '**.blade.php',
        '**/__snapshots__/*',
        '**/Fixtures/*',
        '**/vendor/*',
        __DIR__.'/app/Support/Http/',
        __DIR__.'/bootstrap/cache/',
        __DIR__.'/resources/lang/',
        __FILE__,
    ])
    ->withCache(__DIR__.'/.build/rector/')
    ->withParallel()
    ->withoutParallel()
    // ->withImportNames(importNames: false)
    ->withImportNames(importDocBlockNames: false, importShortClasses: false)
    ->withFluentCallNewLine()
    ->withAttributesSets(phpunit: true, all: true)
    ->withComposerBased(phpunit: true)
    ->withPhpVersion(PhpVersion::PHP_83)
    // ->withDowngradeSets(php83: true)
    ->withPhpSets(php83: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        naming: true,
        instanceOf: true,
        earlyReturn: true,
        carbon: true,
        rectorPreset: true,
        phpunitCodeQuality: true,
    )
    ->withSets([
        PHPUnitSetList::PHPUNIT_110,
        LaravelSetList::LARAVEL_120,
        ...collect((new ReflectionClass(LaravelSetList::class))->getConstants(ReflectionClassConstant::IS_PUBLIC))
            ->reject(
                static fn (string $constant, string $name): bool => \in_array(
                    $name,
                    ['LARAVEL_STATIC_TO_INJECTION', 'LARAVEL_'],
                    true
                ) || preg_match('/^LARAVEL_\d{2,3}$/', $name)
            )
            // ->dd()
            ->values()
            ->all(),
    ])
    ->withRules([
        // AddOverrideAttributeToOverriddenMethodsRector::class,
        ArraySpreadInsteadOfArrayMergeRector::class,
        ClassHandleMethodRector::class,
        JsonThrowOnErrorRector::class,
        SortAssociativeArrayByKeyRector::class,
        StaticArrowFunctionRector::class,
        StaticClosureRector::class,
        ...classes()
            ->filter(static fn (string $class): bool => str_starts_with($class, 'RectorLaravel\Rector'))
            ->filter(static fn (string $class): bool => (new ReflectionClass($class))->isInstantiable())
            // ->dd()
            ->values()
            ->all(),
    ])
    ->withConfiguredRule(RemoveAnnotationRector::class, [
        'codeCoverageIgnore',
        'phpstan-ignore',
        'phpstan-ignore-next-line',
        'psalm-suppress',
    ])
    ->withConfiguredRule(ReturnTypeWillChangeRector::class, [
        new ClassMethodReference(ArrayAccess::class, 'offsetGet'),
    ])
    ->registerService(className: ParentConnectingVisitor::class, tag: ScopeResolverNodeVisitorInterface::class)
    ->withConfiguredRule(RenameToPsrNameRector::class, [
        // '*',
        'static::PARENT_ID',
        'parent',
        'DB',
        'URL',
        'Debug',
        'Info',
        'Notice',
        'Warning',
        'Error',
        'Critical',
        'Alert',
        'Emergency',
        'PDO',
        'OK',
        'FAILING',
        'WARNING',
        'Value',
        'MIT',
        'new_password',
        'current_password',
        'OS',
        'deleted_at',
        'False',
        'True',
        'DAILY',
        'WEEKLY',
        'MONTHLY',
        'string',
        'range_keys',
        'extra_attributes',
        'creator_id',
        'seen_at',
        'CSP',
        'email_verified_at',
        'created_at',
        'updated_at',
        'is_developer',
        'pStmt_ClassMethod',
    ])
    ->withConfiguredRule(RenameClassRector::class, [
        Carbon::class => IlluminateCarbon::class,
    ])
    // ->withConfiguredRule(FuncCallToStaticCallRector::class, [
    //     new FuncCallToStaticCall('request', Illuminate\Support\Facades\Request::class, 'getFacadeRoot'),
    //     new FuncCallToStaticCall('str', Str::class, 'of'),
    // ])
    // ->withConfiguredRule(StaticCallToFuncCallRector::class, [
    //     new StaticCallToFuncCall(Str::class, 'of', 'str'),
    // ])
    // ->withConfiguredRule(
    //     RenameFunctionRector::class,
    //     [
    //         // 'app' => 'resolve',
    //         'faker' => 'fake',
    //         'Pest\Faker\fake' => 'fake',
    //         'Pest\Faker\faker' => 'faker',
    //         'test' => 'it',
    //     ] + array_reduce(
    //         [
    //             'env_explode',
    //             'json_pretty_encode',
    //             'make',
    //             'rescue',
    //         ],
    //         static function (array $carry, string $func): array {
    //             /** @see https://github.com/laravel/framework/blob/11.x/src/Illuminate/Support/functions.php */
    //             $carry[$func] = "App\\Support\\$func";
    //
    //             return $carry;
    //         },
    //         []
    //     )
    // )
    ->withSkip([
        AddOverrideAttributeToOverriddenMethodsRector::class,
        ChangeOrIfContinueToMultiContinueRector::class,
        DisallowedEmptyRuleFixerRector::class,
        NullToStrictStringFuncCallArgRector::class,
        PreferPHPUnitThisCallRector::class,
        RenameForeachValueVariableToMatchExprVariableRector::class,
        RenameForeachValueVariableToMatchMethodCallReturnTypeRector::class,
        RenameParamToMatchTypeRector::class,
        RenamePropertyToMatchTypeRector::class,
        RenameVariableToMatchMethodCallReturnTypeRector::class,
        RenameVariableToMatchNewTypeRector::class,

        EncapsedStringsToSprintfRector::class,
        ExplicitBoolCompareRector::class,
        LogicalToBooleanRector::class,
        NewlineAfterStatementRector::class,
        ParenthesizeNestedTernaryRector::class,
        ReturnBinaryOrToEarlyReturnRector::class,
        WrapEncapsedVariableInCurlyBracesRector::class,
    ])
    ->withSkip([
        EnvVariableToEnvHelperRector::class,
        ServerVariableToRequestFacadeRector::class,

        DispatchToHelperFunctionsRector::class,
        EmptyToBlankAndFilledFuncRector::class,
        HelperFuncCallToFacadeClassRector::class,
        ModelCastsPropertyToCastsMethodRector::class,
        TypeHintTappableCallRector::class,
    ])
    ->withSkip([
        CompleteDynamicPropertiesRector::class => $mixinsPath = [
            __DIR__.'/app/Support/Mixins/',
        ],
        RemoveExtraParametersRector::class => $mixinsPath,
        StaticArrowFunctionRector::class => $staticClosureSkipPaths = [
            __DIR__.'/app/Support/helpers.php',
            // __DIR__.'/routes/console.php',
            __DIR__.'/tests/',
        ],
        StaticClosureRector::class => $staticClosureSkipPaths,
        SortAssociativeArrayByKeyRector::class => [
            __DIR__.'/app/',
            __DIR__.'/bootstrap/',
            __DIR__.'/config/',
            __DIR__.'/database/',
            __DIR__.'/public/',
            __DIR__.'/resources/',
            __DIR__.'/routes/',
            __DIR__.'/tests/',
        ],
    ]);
