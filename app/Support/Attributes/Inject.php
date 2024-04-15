<?php

declare(strict_types=1);

namespace App\Support\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Inject
{
    public function __construct(public ?string $abstract = null) {}
}
