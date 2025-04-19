<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
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
            __DIR__.'/app/',
        ],
        false
    )
    ->addPathsToExclude([
        __DIR__.'/app/Support/Rectors/',
        // __DIR__.'/app/Support/ComposerScripts.php',
        __DIR__.'/tests/',
    ])
    ->ignoreErrors([
        ErrorType::UNUSED_DEPENDENCY,
        ErrorType::SHADOW_DEPENDENCY,
    ])
    ->ignoreErrorsOnPackages(
        [
            'fakerphp/faker',
            'guanguans/ai-commit',
            'laravel/telescope',
        ],
        [ErrorType::DEV_DEPENDENCY_IN_PROD]
    )
    ->ignoreErrorsOnPaths(
        [
            __DIR__.'/app/Providers/UnlessProductionServiceProvider.php',
            __DIR__.'/app/Providers/TelescopeServiceProvider.php',
            // __DIR__.'/app/Providers/WhenLocalServiceProvider.php',
        ],
        [
            ErrorType::DEV_DEPENDENCY_IN_PROD,
        ]
    );
