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
 * @see https://github.com/top-think/think-annotation
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
// #️⃣[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final readonly class Autowired
{
    public function __construct(
        public ?string $propertyType = null,
        public array $parameters = []
    ) {}
}
