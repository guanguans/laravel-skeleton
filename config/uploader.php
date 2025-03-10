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

return [
    /*
     * Base uri for $response->url
     */
    'base_uri' => null,

    'strategies' => [
        /*
         * default strategy.
         */
        'default' => [
            /*
             * The form name for file.
             */
            'name' => 'file',

            /*
             * Allowed MIME types.
             */
            'mimes' => ['image/jpeg', 'image/png', 'image/bmp', 'image/gif'],

            /*
             * The disk name to store file, the value is key of `disks` in `config/filesystems.php`
             */
            'disk' => env('FILESYSTEM_DRIVER', 'public'),

            /*
             * Default directory template.
             * Variables:
             *  - `Y`   Year, example: 2019
             *  - `m`   Month, example: 04
             *  - `d`   Date, example: 08
             *  - `H`   Hour, example: 12
             *  - `i`   Minute, example: 03
             *  - `s`   Second, example: 12
             */
            'directory' => 'uploads/{Y}/{m}/{d}',

            /*
             * File size limit
             */
            'max_size' => '2m',

            /*
             * Strategy of filename.
             *
             * Available:
             *  - `random` Use random string as filename.
             *  - `md5_file` Use md5 of file as filename.
             *  - `original` Use the origin client file name.
             */
            'filename_type' => 'md5_file',
        ],

        /*
         * You can create custom strategy to override the default strategy.
         */
        'avatar' => [
            'directory' => 'avatars/{Y}/{m}/{d}',
        ],

        // ...
    ],
];
