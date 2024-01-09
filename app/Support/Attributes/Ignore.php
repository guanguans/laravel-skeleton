<?php

declare(strict_types=1);

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
