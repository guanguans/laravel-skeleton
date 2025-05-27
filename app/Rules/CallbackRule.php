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

namespace App\Rules;

use App\Casts\CallbackGetCast;

final class CallbackRule extends Rule
{
    private array $callbackArgs;

    /**
     * @param callable|string $callback
     * @param int|numeric-string $idxOfAttrValInCallbackArgs
     */
    public function __construct(
        private readonly mixed $callback,
        private int|string $idxOfAttrValInCallbackArgs = 0,
        mixed ...$callbackArgs
    ) {
        $this->idxOfAttrValInCallbackArgs = (int) $this->idxOfAttrValInCallbackArgs;
        $this->callbackArgs = $callbackArgs;
    }

    /**
     * @throws \Throwable
     */
    #[\Override]
    public function passes(string $attribute, mixed $value): bool
    {
        array_splice($this->callbackArgs, $this->idxOfAttrValInCallbackArgs, 0, $value);

        return (bool) \call_user_func_array(
            CallbackGetCast::resolveCallback($this->callback),
            $this->callbackArgs
        );
    }
}
