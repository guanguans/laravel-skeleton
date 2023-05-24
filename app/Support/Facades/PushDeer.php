<?php

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\Client\Response messagePush(string $text, string $desp = '', string $type = 'markdown')
 * @method static \Illuminate\Http\Client\Response messageList(int $limit = 10)
 * @method static void ddRequestData()
 * @method static void dumpRequestData()
 * @method static callable buildLogMiddleware(\Psr\Log\LoggerInterface|null $logger = null, \GuzzleHttp\MessageFormatter|null $formatter = null, string $logLevel = 'info')
 * @method static void tapDefaultPendingRequest(callable $callback)
 * @method static \Illuminate\Http\Client\PendingRequest cloneDefaultPendingRequest()
 * @method static \App\Support\PushDeer|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\PushDeer|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\PushDeer|\Illuminate\Support\HigherOrderTapProxy tap(callable|null $callback = null)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \App\Support\PushDeer
 */
class PushDeer extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \App\Support\PushDeer::class;
    }
}
