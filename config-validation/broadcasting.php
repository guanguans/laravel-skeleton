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

use AshAllenDesign\ConfigValidator\Services\Rule;

return [
    Rule::make('default')->rules(['string', 'in:pusher,redis,log,null']),

    Rule::make('connections')->rules(['array']),

    Rule::make('connections.pusher')->rules(['array']),

    Rule::make('connections.pusher.driver')->rules(['string']),

    Rule::make('connections.pusher.key')->rules(['string']),

    Rule::make('connections.pusher.secret')->rules(['string']),

    Rule::make('connections.pusher.app_id')->rules(['string']),

    Rule::make('connections.pusher.options')->rules(['array']),

    Rule::make('connections.pusher.options.cluster')->rules(['string']),

    Rule::make('connections.pusher.options.useTLS')->rules(['bool']),

    Rule::make('connections.redis')->rules(['array']),

    Rule::make('connections.redis.driver')->rules(['string']),

    Rule::make('connections.redis.connection')->rules(['string']),

    Rule::make('connections.log')->rules(['array']),

    Rule::make('connections.log.driver')->rules(['string']),

    Rule::make('connections.null')->rules(['array']),

    Rule::make('connections.null.driver')->rules(['string']),
];
