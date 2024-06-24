<?php

declare(strict_types=1);

// $argv = array_merge(
//     $argv,
//     array_map(
//         static fn ($f) => str_replace(['app', '/', '.php'], ['App', '\\', ''], $f),
//         glob('app/Support/Facades/*.php')
//     )
// );

// $classes = escapeshellarg(
//     <<<'CODE'
//         echo implode(' ', array_map(fn ($f) => str_replace(['app', '/', '.php'], ['App', '\\', ''], $f), glob('app/Support/Facades/*.php')));
//         CODE
// );

require __DIR__.'/vendor/autoload.php';

require __DIR__.'/vendor/bin/facade.php';
