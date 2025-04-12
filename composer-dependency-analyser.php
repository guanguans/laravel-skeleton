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
    ->ignoreErrorsOnPackages(
        [
            'phar-io/version',
        ],
        [ErrorType::SHADOW_DEPENDENCY],
    );
