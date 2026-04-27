<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Attribute;

use Illuminate\Support\Arr;

/**
 * @see https://github.com/TheFlowByte/laravel-macro-attribute
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class Mixin
{
    /** @var list<class-string<\Illuminate\Support\Traits\Macroable>> */
    public array $classes;

    /**
     * @param class-string<\Illuminate\Support\Traits\Macroable>|list<class-string<\Illuminate\Support\Traits\Macroable>> $classes
     */
    public function __construct(
        array|string $classes,
        public bool $replace = true
    ) {
        $this->classes = Arr::wrap($classes);
    }
}
