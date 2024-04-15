<?php

declare(strict_types=1);

namespace App\Support\Attributes;

/**
 * @see https://github.com/top-think/think-annotation
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Inject
{
    public function __construct(public ?string $abstract = null) {}
}
