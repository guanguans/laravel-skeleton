<?php

declare(strict_types=1);

namespace App\Support\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class After extends Before {}
