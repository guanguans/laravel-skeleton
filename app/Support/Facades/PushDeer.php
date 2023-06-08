<?php

declare(strict_types=1);

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\Client\Response messagePush(string $text, string $desp = '', string $type = 'markdown')
 * @method static \Illuminate\Http\Client\Response messageList(int $limit = 10)
 * @method static void ddRequestData()
 * @method static void dumpRequestData()
 * @method static callable buildLogMiddleware(null|\Psr\Log\LoggerInterface $logger = null, null|\GuzzleHttp\MessageFormatter $formatter = null, string $logLevel = 'info')
 * @method static void tapDefaultPendingRequest(callable $callback)
 * @method static \Illuminate\Http\Client\PendingRequest cloneDefaultPendingRequest()
 * @method static \App\Support\PushDeer|mixed when(null|\Closure|mixed $value = null, null|callable $callback = null, null|callable $default = null)
 * @method static \App\Support\PushDeer|mixed unless(null|\Closure|mixed $value = null, null|callable $callback = null, null|callable $default = null)
 * @method static \App\Support\PushDeer|\Illuminate\Support\HigherOrderTapProxy tap(null|callable $callback = null)
 * @method static void macro(string $name, callable|object $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \App\Support\PushDeer
 */
class PushDeer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Support\PushDeer::class;
    }
}
