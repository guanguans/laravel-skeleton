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
    Rule::make('driver')->rules(['string', 'in:bcrypt,argon,argon2id']),

    Rule::make('bcrypt')->rules(['array']),
    Rule::make('bcrypt.rounds')->rules(['integer']),

    Rule::make('argon')->rules(['array']),
    Rule::make('argon.memory')->rules(['integer']),
    Rule::make('argon.threads')->rules(['integer']),
    Rule::make('argon.time')->rules(['integer']),
];
