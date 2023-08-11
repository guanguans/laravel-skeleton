<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Names
    |--------------------------------------------------------------------------
    |
    | This option determines the handling of route names.
    |
    */

    'names' => [
        /*
        |--------------------------------------------------------------------------
        | Exclude Names
        |--------------------------------------------------------------------------
        |
        | This option specifies the names of the routes that will be excluded
        | from the conversion.
        |
        */

        'exclude' => [
            '__clockwork*',
            '_debugbar*',
            '_ignition*',
            'horizon*',
            'pretty-routes*',
            'sanctum*',
            'telescope*',
        ],
    ],
];
