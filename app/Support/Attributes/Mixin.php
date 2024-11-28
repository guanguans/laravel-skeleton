<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Attributes;

/**
 * @see https://github.com/TheFlowByte/laravel-macro-attribute
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
readonly class Mixin
{
    /**
     * @param  class-string<\Illuminate\Support\Traits\Macroable>  $class
     */
    public function __construct(
        public string $class,
        public bool $replace = true
    ) {}
}
