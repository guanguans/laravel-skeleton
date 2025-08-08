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

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class CallbackGetCast implements CastsAttributes
{
    private array $callbackArgs;

    public function __construct(
        private readonly mixed $callback,
        private readonly int|string $idxOfAttrValInCallbackArgs = 0,
        mixed ...$callbackArgs
    ) {
        $this->callbackArgs = $callbackArgs;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }

    /**
     * @throws \Throwable
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        array_splice($this->callbackArgs, $this->idxOfAttrValInCallbackArgs, 0, $value);

        return \call_user_func_array(resolve_callback($this->callback), $this->callbackArgs);
    }
}
