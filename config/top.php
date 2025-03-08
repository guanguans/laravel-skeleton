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
    |--------------------------------------------------------------------------
    | Redis Connection
    |--------------------------------------------------------------------------
    |
    | Specify the Redis database connection from config/database.php
    | that Top will use to save data.
    | The default value is suitable for most applications.
    |
    */

    'connection' => env('TOP_REDIS_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Recording Mode
    |--------------------------------------------------------------------------
    |
    | Determine when Top should record application metrics based on this value.
    | By default, Top only listens to your application when it is running.
    | If you want to access metrics through the facade, you can select the "always" mode.
    |
    | Available Modes: "runtime", "always"
    |
    */

    'recording_mode' => env('TOP_RECORDING_MODE', 'runtime'),
];
