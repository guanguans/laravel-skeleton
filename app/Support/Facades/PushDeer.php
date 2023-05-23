<?php

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\Client\Response messagePush(string $text, string $desp = '', string $type = 'markdown')
 * @method static \Illuminate\Http\Client\Response messageList(int $limit = 10)
 * @method static void dd()
 * @method static void dump()
 * @method static void ddRequestData()
 * @method static void dumpRequestData()
 * @method static void withLoggerMiddleware(\Psr\Log\LoggerInterface|null $logger = null, \GuzzleHttp\MessageFormatterInterface|null $formatter = null, string $logLevel = 'info')
 * @method static void tapPendingRequest(callable $callback)
 * @method static callable buildLoggerMiddleware(\Psr\Log\LoggerInterface|null $logger = null, \GuzzleHttp\MessageFormatterInterface|null $formatter = null, string $logLevel = 'info')
 * @method static \App\Support\PushDeer|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\PushDeer|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\PushDeer|\Illuminate\Support\HigherOrderTapProxy tap(callable|null $callback = null)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static array validateDataWith(\Illuminate\Contracts\Validation\Validator|array $validator, array $data)
 * @method static array validateData(array $data, array $rules, array $messages = [], array $customAttributes = [])
 * @method static array validateDataWithBag(string $errorBag, array $data, array $rules, array $messages = [], array $customAttributes = [])
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
