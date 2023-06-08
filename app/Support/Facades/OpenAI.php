<?php

declare(strict_types=1);

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string hydrateData(string $data)
 * @method static \Illuminate\Http\Client\Response completions(array $parameters, null|callable $writer = null)
 * @method static \Illuminate\Http\Client\Response chatCompletions(array $parameters, null|callable $writer = null)
 * @method static \Illuminate\Http\Client\Response models()
 * @method static \Illuminate\Support\Collection completionsByCurl(array $data, null|callable $writer = null)
 * @method static void ddRequestData()
 * @method static void dumpRequestData()
 * @method static callable buildLogMiddleware(null|\Psr\Log\LoggerInterface $logger = null, null|\GuzzleHttp\MessageFormatter $formatter = null, string $logLevel = 'info')
 * @method static void tapDefaultPendingRequest(callable $callback)
 * @method static \Illuminate\Http\Client\PendingRequest cloneDefaultPendingRequest()
 * @method static \App\Support\OpenAI|mixed when(null|\Closure|mixed $value = null, null|callable $callback = null, null|callable $default = null)
 * @method static \App\Support\OpenAI|mixed unless(null|\Closure|mixed $value = null, null|callable $callback = null, null|callable $default = null)
 * @method static \App\Support\OpenAI|\Illuminate\Support\HigherOrderTapProxy tap(null|callable $callback = null)
 * @method static void macro(string $name, callable|object $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \App\Support\OpenAI
 */
class OpenAI extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Support\OpenAI::class;
    }
}
