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
 * @method static \Illuminate\Http\Client\PendingRequest pendingRequest(callable|null $callback = null)
 * @method static \App\Support\Clients\PushDeer|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\Clients\PushDeer|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static mixed withLocale(string $locale, \Closure $callback)
 * @method static \Illuminate\Support\HigherOrderTapProxy|\App\Support\Clients\PushDeer tap(callable|null $callback = null)
 *
 * @see \App\Support\Clients\PushDeer
 */
class PushDeer extends Facade
{
    #[\Override]
    protected static function getFacadeAccessor()
    {
        return \App\Support\Clients\PushDeer::class;
    }
}
