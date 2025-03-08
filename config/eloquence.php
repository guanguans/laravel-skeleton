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
    'logging' => [
        'enabled' => env('ELOQUENCE_LOGGING_ENABLED', false),
        'driver' => env('ELOQUENCE_LOGGING_DRIVER', env('LOG_CHANNEL', 'stack')),
    ],
];
