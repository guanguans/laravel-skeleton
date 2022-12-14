<?php

/** @noinspection PhpParamsInspection */

namespace App\Support;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * @see https://chat.openai.com/chat
 */
class ChatGPT extends FoundationSdk
{
    public function refreshAccessToken()
    {
        return $this->pendingRequest
            ->withHeaders([
                'cookie' => "__Secure-next-auth.session-token={$this->config['session_token']}",
            ])
            ->get('api/auth/session');
    }

    public function conversation(string $prompt, ?string $conversationId = null, ?string $messageId = null)
    {
        // 待优化
        if (! Cache::get(__CLASS__)) {
            Cache::put(
                __CLASS__,
                $this->refreshAccessToken()->json('accessToken'),
                $this->config['access_token_cache_ttl']
            );
        }

        $accessToken = Cache::get(__CLASS__);

        $originalResponse = $this->pendingRequest
            ->withOptions([
                // 'stream' => true,
            ])
            ->withHeaders([
                'Authorization' => "Bearer $accessToken",
            ])
            ->post('backend-api/conversation', [
                'action' => 'next',
                'conversation_id' => $conversationId,
                'messages' => [
                    [
                        'id' => $messageId ?? Str::uuid(),
                        'role' => 'user',
                        'content' => [
                            'content_type' => 'text',
                            'parts' => [$prompt],
                        ],
                    ],
                ],
                'model' => 'text-davinci-002-render',
                'parent_message_id' => Str::uuid(),
            ]);

        $contents = \str($originalResponse->getBody()->getContents())
            ->explode("\n\n")
            ->last(function (string $data) {
                return $data && $data !== 'data: [DONE]';
            });

        return new Response(
            $originalResponse
                ->toPsrResponse()
                ->withBody(Utils::streamFor(substr($contents, 6)))
        );
    }

    protected function validateConfig(array $config): array
    {
        return array_merge(
            [
                'http_options' => [],
                'base_url' => 'https://chat.openai.com',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
                'access_token_cache_ttl' => 3600,
            ],
            $this->validateData($config, [
                'http_options' => 'array',
                'session_token' => 'required|string',
                'base_url' => 'string',
                'user_agent' => 'string',
                'access_token_cache_ttl' => 'int',
            ])
        );
    }

    protected function buildPendingRequest(array $config): PendingRequest
    {
        return Http::baseUrl($config['base_url'])
            ->asJson()
            ->throw()
            ->withUserAgent($this->config['user_agent'])
            ->withOptions($config['http_options']);
    }
}
