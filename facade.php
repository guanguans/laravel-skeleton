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

require __DIR__.'/vendor/autoload.php';

$argv = [...$argv, ...array_map(
    static fn ($f): string => str_replace(['app', '/', '.php'], ['App', '\\', ''], $f),
    glob('app/{,Support/ApiResponse}/Facades/*.php', \GLOB_BRACE)
)];

// $classes = escapeshellarg(
//     <<<'CODE'
//         echo implode(' ', array_map(fn ($f) => str_replace(['app', '/', '.php'], ['App', '\\', ''], $f), glob('app/Support/Facades/*.php')));
//         CODE
// );

require __DIR__.'/vendor/bin/facade.php';
