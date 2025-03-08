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
     * The storage config
     */
    'storage' => [
        /*
         * Returns the folder name of the chunks. The location is in storage/app/{folder_name}
         */
        'chunks' => 'chunks',
        'disk' => 'local',
    ],
    'clear' => [
        /*
         * How old chunks we should delete
         */
        'timestamp' => '-3 HOURS',
        'schedule' => [
            'enabled' => true,
            'cron' => '25 * * * *', // run every hour on the 25th minute
        ],
    ],
    'chunk' => [
        // setup for the chunk naming setup to ensure same name upload at same time
        'name' => [
            'use' => [
                'session' => true, // should the chunk name use the session id? The uploader must send cookie!,
                'browser' => false, // instead of session we can use the ip and browser?
            ],
        ],
    ],
    'handlers' => [
        // A list of handlers/providers that will be appended to existing list of handlers
        'custom' => [],
        // Overrides the list of handlers - use only what you really want
        'override' => [
            // \Pion\Laravel\ChunkUpload\Handler\DropZoneUploadHandler::class
        ],
    ],
];
