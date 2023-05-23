<?php

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void refreshAccessToken()
 * @method static void conversation(string $prompt, string|null $conversationId = null, string|null $messageId = null)
 * @method static void dd()
 * @method static void dump()
 * @method static void ddRequestData()
 * @method static void dumpRequestData()
 * @method static void withLoggerMiddleware(\Psr\Log\LoggerInterface|null $logger = null, \GuzzleHttp\MessageFormatterInterface|null $formatter = null, string $logLevel = 'info')
 * @method static void tapPendingRequest(callable $callback)
 * @method static callable buildLoggerMiddleware(\Psr\Log\LoggerInterface|null $logger = null, \GuzzleHttp\MessageFormatterInterface|null $formatter = null, string $logLevel = 'info')
 * @method static \App\Support\ChatGPT|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\ChatGPT|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \App\Support\ChatGPT|\Illuminate\Support\HigherOrderTapProxy tap(callable|null $callback = null)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static array validateDataWith(\Illuminate\Contracts\Validation\Validator|array $validator, array $data)
 * @method static array validateData(array $data, array $rules, array $messages = [], array $customAttributes = [])
 * @method static array validateDataWithBag(string $errorBag, array $data, array $rules, array $messages = [], array $customAttributes = [])
 *
 * @see \App\Support\ChatGPT
 */
class ChatGPT extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \App\Support\ChatGPT::class;
    }
}
