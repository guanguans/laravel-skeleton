<?php

declare(strict_types=1);

use App\Support\Rectors\RenameToPsrNameRector;
use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
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
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
use Rector\Set\ValueObject\DowngradeLevelSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->importNames(false, false);
    $rectorConfig->importShortClasses(false);
    // $rectorConfig->disableParallel();
    $rectorConfig->parallel(300);
    $rectorConfig->phpstanConfig(__DIR__.'/phpstan.neon');
    $rectorConfig->phpVersion(PhpVersion::PHP_81);

    // $rectorConfig->cacheClass(FileCacheStorage::class);
    // $rectorConfig->cacheDirectory(__DIR__.'/build/rector');
    // $rectorConfig->containerCacheDirectory(__DIR__.'/build/rector');
    // $rectorConfig->disableParallel();
    // $rectorConfig->fileExtensions(['php']);
    // $rectorConfig->indent(' ', 4);
    // $rectorConfig->memoryLimit('2G');
    // $rectorConfig->noDiffs();
    // $rectorConfig->removeUnusedImports();

    $rectorConfig->bootstrapFiles([
        // __DIR__.'/vendor/autoload.php',
    ]);

    $rectorConfig->autoloadPaths([
        // __DIR__.'/vendor/autoload.php',
    ]);

    $rectorConfig->paths([
        __DIR__.'/app',
        __DIR__.'/config',
        __DIR__.'/routes',
        __DIR__.'/.*.php',
        __DIR__.'/*.php',
        __DIR__.'/composer-updater',
    ]);

    $rectorConfig->skip([
        // rules
        CompleteDynamicPropertiesRector::class,
        DisallowedEmptyRuleFixerRector::class,
        EncapsedStringsToSprintfRector::class,
        ExplicitBoolCompareRector::class,
        InlineIfToExplicitIfRector::class,
        JsonThrowOnErrorRector::class,
        LogicalToBooleanRector::class,
        NullToStrictStringFuncCallArgRector::class,
        PostIncDecToPreIncDecRector::class,
        RemoveExtraParametersRector::class,
        RemoveUnusedPrivateMethodRector::class,
        SplitDoubleAssignRector::class,
        WrapEncapsedVariableInCurlyBracesRector::class,

        RemoveUnusedVariableInCatchRector::class => [
            __DIR__.'/app/Support/Macros/CommandMacro.php',
        ],
        MakeInheritedMethodVisibilitySameAsParentRector::class => [
            __DIR__.'/app/Admin/Actions/Show',
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
        StaticArrowFunctionRector::class => [
            __DIR__.'/app/Admin/Controllers',
            __DIR__.'/app/Admin/Forms',
            __DIR__.'/tests',
        ],
        StaticClosureRector::class => [
            __DIR__.'/app/Support/helpers.php',
            __DIR__.'/app/Admin/Controllers',
            __DIR__.'/app/Admin/Forms',
            __DIR__.'/tests',
        ],

        // paths
        __DIR__.'/.phpstorm.meta.php',
        __DIR__.'/_ide_helper.php',
        __DIR__.'/_ide_helper_models.php',
        __DIR__.'/app/Console/Commands/ParsePHPFileToASTCommand.php',
        __DIR__.'/app/Support/Http',
        __DIR__.'/dcat_admin_ide_helper.php',
        __DIR__.'/deploy.example.php',
        __DIR__.'/deploy.php',
        '**/Fixture*',
        '**/Fixture/*',
        '**/Fixtures*',
        '**/Fixtures/*',
        '**/Stub*',
        '**/Stub/*',
        '**/Stubs*',
        '**/Stubs/*',
        '**/Source*',
        '**/Source/*',
        '**/Expected/*',
        '**/Expected*',
        '**/__snapshots__/*',
        '**/__snapshots__*',
    ]);

    $rectorConfig->sets([
        DowngradeLevelSetList::DOWN_TO_PHP_81,
        LevelSetList::UP_TO_PHP_81,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        // SetList::STRICT_BOOLEANS,
        // SetList::GMAGICK_TO_IMAGICK,
        // SetList::NAMING,
        // SetList::PRIVATIZATION,
        // SetList::TYPE_DECLARATION,
        // SetList::EARLY_RETURN,
        SetList::INSTANCEOF,
    ]);

    $rectorConfig->rules([
        InlineConstructorDefaultToPropertyRector::class,
    ]);

    $rectorConfig->ruleWithConfiguration(RenameFunctionRector::class, [
        'test' => 'it',
    ]);

    $rectorConfig->ruleWithConfiguration(RenameToPsrNameRector::class, [
        '_*',
    ]);

    // (function ($rectorConfig): void {
    //     $rectorConfig->ruleConfigurations[AnnotationToAttributeRector::class] = array_filter(
    //         $rectorConfig->ruleConfigurations[AnnotationToAttributeRector::class],
    //         static fn (AnnotationToAttribute $annotationToAttribute): bool => ! in_array(
    //             $annotationToAttribute->getAttributeClass(),
    //             [
    //                 CodeCoverageIgnore::class,
    //             ],
    //             true
    //         )
    //     );
    // })->call($rectorConfig, $rectorConfig);
};
