<?php

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\Client\Response refreshAccessToken()
 * @method static \Illuminate\Http\Client\Response conversation(string $prompt, ?string $conversationId = null, ?string $messageId = null)
 *
 * @mixin \App\Support\ChatGPT
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
