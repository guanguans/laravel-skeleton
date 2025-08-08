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
        private readonly int $idxOfAttrValInCallbackArgs = 0,
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

        return \call_user_func_array(self::resolveCallback($this->callback), $this->callbackArgs);
    }

    /**
     * @see https://github.com/PHP-DI/Invoker/blob/master/src/CallableResolver.php
     * @see \Illuminate\Container\Container::call()
     *
     * @noinspection RedundantDocCommentTagInspection
     * @noinspection DebugFunctionUsageInspection
     *
     * @throws \Throwable
     *
     * @return callable(mixed...): bool
     */
    public static function resolveCallback(callable|string $callback): callable
    {
        if (\is_callable($callback)) {
            return $callback;
        }

        $segments = explode('@', $callback, 2);

        if (\is_callable($segments)) {
            return $segments;
        }

        $callbackName = var_export($callback, true);

        throw_if(
            \count($segments) !== 2 || !method_exists($segments[0], $segments[1]),
            \InvalidArgumentException::class,
            "Invalid callback [$callbackName]."
        );

        try {
            return [resolve($segments[0]), $segments[1]];
        } catch (\Throwable $throwable) {
            throw new \InvalidArgumentException(
                "Invalid callback [$callbackName] [{$throwable->getMessage()}].",
                $throwable->getCode(),
                $throwable
            );
        }
    }
}
