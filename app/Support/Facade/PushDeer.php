<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\Client\Response messagePush(string $text, string $desp = '', string $type = 'markdown')
 * @method static \App\Support\Client\PushDeer ddPendingRequest(mixed ...$args)
 * @method static \App\Support\Client\PushDeer dumpPendingRequest(mixed ...$args)
 * @method static \Illuminate\Http\Client\PendingRequest clonePendingRequest(callable|null $callback = null)
 * @method static \Illuminate\Http\Client\PendingRequest pendingRequest(callable|null $callback = null, bool $clone = false)
 * @method static \App\Support\Client\PushDeer|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\Client\PushDeer|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static never dd(mixed ...$args)
 * @method static \App\Support\Client\PushDeer dump(mixed ...$args)
 * @method static mixed withLocale(string $locale, \Closure $callback)
 * @method static \Illuminate\Support\HigherOrderTapProxy|\App\Support\Client\PushDeer tap(callable|null $callback = null)
 *
 * @see \App\Support\Client\PushDeer
 */
final class PushDeer extends Facade
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected static function getFacadeAccessor(): string
    {
        return \App\Support\Client\PushDeer::class;
    }
}
