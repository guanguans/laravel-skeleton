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
    Rule::make('paths')->rules(['array']),

    Rule::make('allowed_methods')->rules(['array']),

    Rule::make('allowed_origins')->rules(['array']),

    Rule::make('allowed_origins_patterns')->rules(['array']),

    Rule::make('allowed_headers')->rules(['array']),

    Rule::make('max_age')->rules(['integer']),

    Rule::make('supports_credentials')->rules(['bool']),
];
