<?php

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\Client\Response completions(array $data, callable|null $writer = null)
 * @method static \Illuminate\Support\Collection completionsByCurl(array $data, callable|null $writer = null)
 * @method static void dd()
 * @method static void dump()
 * @method static void ddRequestData()
 * @method static void dumpRequestData()
 * @method static void withLoggerMiddleware(\Psr\Log\LoggerInterface|null $logger = null, \GuzzleHttp\MessageFormatterInterface|null $formatter = null, string $logLevel = 'info')
 * @method static void tapPendingRequest(callable $callback)
 * @method static callable buildLoggerMiddleware(\Psr\Log\LoggerInterface|null $logger = null, \GuzzleHttp\MessageFormatterInterface|null $formatter = null, string $logLevel = 'info')
 * @method static \App\Support\OpenAI|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\OpenAI|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\OpenAI|\Illuminate\Support\HigherOrderTapProxy tap(callable|null $callback = null)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \App\Support\OpenAI
 */
class OpenAI extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \App\Support\OpenAI::class;
    }
}
