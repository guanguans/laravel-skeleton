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

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\Client\Response messagePush(string $text, string $desp = '', string $type = 'markdown')
 * @method static \Illuminate\Http\Client\Response messageList(int $limit = 10)
 * @method static \App\Support\PushDeer ddLaravelData()
 * @method static \App\Support\PushDeer dumpLaravelData()
 * @method static \App\Support\PushDeer tapPendingRequest(callable $callback)
 * @method static \Illuminate\Http\Client\PendingRequest clonePendingRequest()
 * @method static \App\Support\PushDeer|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\PushDeer|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static void dd(mixed ...$args)
 * @method static \App\Support\FoundationSdk dump(mixed ...$args)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static \Illuminate\Support\HigherOrderTapProxy|\App\Support\PushDeer tap(callable|null $callback = null)
 *
 * @see \App\Support\PushDeer
 */
class PushDeer extends Facade
{
    #[\Override]
    protected static function getFacadeAccessor()
    {
        return \App\Support\PushDeer::class;
    }
}
