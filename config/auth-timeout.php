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
    /**
     * The session name used to identify if the user has reached the timeout time.
     */
    'session' => 'last_activity_time',

    /**
     * The minutes of idle time before the user is logged out.
     */
    'timeout' => 15,

    /**
     * The event that will be dispatched when a user has timed out.
     */
    'event' => JulioMotol\AuthTimeout\Events\AuthTimedOut::class,
];
