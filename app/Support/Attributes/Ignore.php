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
 * @see https://blog.oussama-mater.tech/php-attributes/
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Ignore
{
    public function __construct(
        public array $in = ['production']
    ) {}
}
