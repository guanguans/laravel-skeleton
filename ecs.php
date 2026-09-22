<?php

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

use Guanguans\PhpCsFixerCustomFixers\Set\SetList;
use Guanguans\PhpCsFixerCustomFixers\Support\Utils;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\FunctionNotation\StaticLambdaFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    // ->withoutParallel()
    ->withPaths(Utils::defaultPaths())
    ->withSkip([
        // StaticLambdaFixer::class => [
        //     __DIR__.'/src/ReleaseWorker/RunComposerScriptsReleaseWorker.php',
        //     __DIR__.'/tests/*Test.php',
        //     __DIR__.'/tests/Pest.php',
        // ],
        PhpCsFixer\Fixer\ClassNotation\FinalPublicMethodForAbstractClassFixer::class => [
            __DIR__.'/app/Support/StreamWrapper/StreamWrapper.php',
        ],
        Symplify\CodingStandard\Fixer\Commenting\FixParamNameTypoFixer::class => [
            __DIR__.'/app/Support/Client/AbstractClient.php',
            __DIR__.'/app/Support/Mixin/QueryBuilder/QueryBuilderMixin.php',
        ],
    ])
    ->withSets([SetList::GUANGUANS])
    ->withConfiguredRule(HeaderCommentFixer::class, Utils::configurationOfHeaderCommentFixer(
        'guanguans/laravel-skeleton',
        '2021',
        __DIR__.'/LICENSE'
    ));
