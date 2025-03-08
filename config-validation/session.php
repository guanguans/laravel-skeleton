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
    Rule::make('driver')->rules(['string', 'in:file,cookie,database,apc,memcached,redis,dynamodb,array']),

    Rule::make('lifetime')->rules(['integer']),

    Rule::make('expire_on_close')->rules(['bool']),

    Rule::make('encrypt')->rules(['bool']),

    Rule::make('files')->rules(['string']),

    Rule::make('connection')->rules(['string', 'nullable']),

    Rule::make('table')->rules(['string']),

    Rule::make('store')->rules(['string', 'nullable']),

    Rule::make('lottery')->rules(['array']),

    Rule::make('cookie')->rules(['string']),

    Rule::make('path')->rules(['string']),

    Rule::make('domain')->rules(['string', 'nullable']),

    Rule::make('secure')->rules(['bool', 'nullable']),

    Rule::make('http_only')->rules(['bool']),

    Rule::make('same_site')->rules(['string', 'nullable', 'in:lax,strict,none']),
];
