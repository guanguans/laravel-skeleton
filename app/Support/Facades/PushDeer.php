<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\Client\Response messagePush(string $text, string $desp = '', string $type = 'markdown')
 * @method static \Illuminate\Http\Client\Response messageList(int $limit = 10)
 * @method static \App\Support\PushDeer ddRequestData()
 * @method static \App\Support\PushDeer dumpRequestData()
 * @method static callable buildLogMiddleware(\Psr\Log\LoggerInterface|null $logger = null, \GuzzleHttp\MessageFormatter|null $formatter = null, string $logLevel = 'info')
 * @method static \App\Support\PushDeer tapDefaultPendingRequest(callable $callback)
 * @method static \Illuminate\Http\Client\PendingRequest cloneDefaultPendingRequest()
 * @method static \App\Support\PushDeer|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\PushDeer|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static void macro(string $name, object|callable $macro, object|callable $macro = null)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static void tap(callable|null $callback = null)
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
