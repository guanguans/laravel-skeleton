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
    Rule::make('mailgun')->rules(['array']),
    Rule::make('mailgun.domain')->rules(['string', 'nullable']),
    Rule::make('mailgun.secret')->rules(['string', 'nullable']),
    Rule::make('mailgun.endpoint')->rules(['string']),

    Rule::make('postmark')->rules(['array']),
    Rule::make('postmark.token')->rules(['string', 'nullable']),

    Rule::make('ses')->rules(['array']),
    Rule::make('ses.key')->rules(['string', 'nullable']),
    Rule::make('ses.secret')->rules(['string', 'nullable']),
    Rule::make('ses.region')->rules(['string']),
];
