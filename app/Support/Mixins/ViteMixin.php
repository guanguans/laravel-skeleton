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

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
use Illuminate\Foundation\Vite;

/**
 * @mixin \Illuminate\Foundation\Vite
 */
#[Mixin(Vite::class)]
class ViteMixin
{
    /**
     * @see https://github.com/dasundev/dasun.dev
     */
    public static function image(): callable
    {
        return fn (string $asset) => $this->asset("resources/images/$asset");
    }
}
