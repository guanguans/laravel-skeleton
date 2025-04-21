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

namespace App\Support\Attributes;

/**
 * @see https://github.com/TheFlowByte/laravel-macro-attribute
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
readonly class Mixin
{
    public function __construct(
        /**
         * @var class-string<\Illuminate\Support\Traits\Macroable>
         */
        public string $class,
        public bool $replace = true
    ) {}
}
