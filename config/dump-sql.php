<?php

/**
 * This file is part of the guanguans/laravel-dump-sql.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

return [
    /*
     * The host to use when listening for debug server connections.
     */
    'host' => env('LISTEN_SQL_SERVER_HOST', 'tcp://127.0.0.1:9913'),
];
