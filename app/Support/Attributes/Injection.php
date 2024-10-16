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
 * @see https://github.com/top-think/think-annotation
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class Injection
{
    public function __construct(
        public ?string $propertyType = null,
        public array $parameters = []
    ) {}
}
