<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

require __DIR__.'/vendor/autoload.php';

$argv = array_merge(
    $argv,
    array_map(
        static fn ($f): string|array => str_replace(['app', '/', '.php'], ['App', '\\', ''], $f),
        glob('app/{,Support/ApiResponse}/Facades/*.php', GLOB_BRACE)
    )
);

// $classes = escapeshellarg(
//     <<<'CODE'
//         echo implode(' ', array_map(fn ($f) => str_replace(['app', '/', '.php'], ['App', '\\', ''], $f), glob('app/Support/Facades/*.php')));
//         CODE
// );

require __DIR__.'/vendor/bin/facade.php';
