<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration)
    ->addPathsToScan(
        [
            __DIR__.'/bootstrap/',
            __DIR__.'/config/',
            __DIR__.'/database/',
            __DIR__.'/public/',
            __DIR__.'/resources/',
            __DIR__.'/routes/',
        ],
        false
    )
    ->addPathsToExclude([
        __DIR__.'/app/Support/',
    ])
    ->ignoreUnknownClasses([
        'Pion\Laravel\ChunkUpload\Handler\ResumableJSUploadHandler',
        'Pion\Laravel\ChunkUpload\Receiver\FileReceiver',
    ])
    ->ignoreErrors([ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnPackages(
        [
            'clue/stream-filter',
            'elasticsearch/elasticsearch',
            'guanguans/laravel-exception-notify',
            'laravel/tinker',
        ],
        [ErrorType::UNUSED_DEPENDENCY]
    )
    ->ignoreErrorsOnPaths(
        [
            __DIR__.'/app/Providers/PackageServiceProvider.php',
            // __DIR__.'/app/Providers/UnlessProductionAggregateServiceProvider.php',
            __DIR__.'/app/Providers/WhenLocalAggregateServiceProvider.php',
            __DIR__.'/app/Providers/WhenTestingAggregateServiceProvider.php',
        ],
        [ErrorType::DEV_DEPENDENCY_IN_PROD]
    );
