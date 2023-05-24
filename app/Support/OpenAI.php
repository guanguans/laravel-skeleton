<?php

namespace App\Support;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Stringable;

/**
 * @see https://beta.openai.com/docs/api-reference/introduction
 */
class OpenAI extends FoundationSdk
{
    public function completions(array $data, ?callable $writer = null): Response
    {
        return (clone $this->pendingRequest)
            ->when(
                static fn (PendingRequest $pendingRequest) => $writer,
                static function (PendingRequest $pendingRequest, ?callable $writer) {
                    $pendingRequest->withOptions([
                        'curl' => [
                            CURLOPT_WRITEFUNCTION => static function (object $ch, string $data) use ($writer) {
                                // \str($data)
                                //     ->replaceFirst('data: ', '')
                                //     ->rtrim()
                                //     ->tap(function (Stringable $stringable) {
                                //         if ($stringable->startsWith('[DONE]')) {
                                //             return;
                                //         }
                                //
                                //         echo Arr::get(json_decode($stringable, true), 'choices.0.text', '');
                                //     });

                                $writer($data, $ch);

                                return strlen($data);
                            },
                        ],
                    ]);
                }
            )
            ->post('completions', $this->validate(
                $data,
                [
                    'model' => [
                        'required',
                        'string',
                        'in:text-davinci-003,text-curie-001,text-babbage-001,text-ada-001,text-embedding-ada-002,code-davinci-002,code-cushman-001,content-filter-alpha',
                    ],
                    // 'prompt' => 'string|array',
                    'prompt' => 'string',
                    'suffix' => 'nullable|string',
                    'max_tokens' => 'integer',
                    'temperature' => 'numeric',
                    'top_p' => 'numeric',
                    'n' => 'integer',
                    'stream' => 'bool',
                    'logprobs' => 'nullable|integer',
                    'echo' => 'bool',
                    // 'stop' => 'nullable|string|array',
                    'stop' => 'nullable|string',
                    'presence_penalty' => 'numeric',
                    'frequency_penalty' => 'numeric',
                    'best_of' => 'integer',
                    'logit_bias' => 'array', // map
                    'user' => 'string|uuid',
                ]
            ));
    }

    public function completionsByCurl(array $data, ?callable $writer = null): Collection
    {
        $options = [
            CURLOPT_URL => "{$this->config['base_url']}/completions",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(
                $this->validate(
                    $data,
                    [
                        'model' => [
                            'required',
                            'string',
                            'in:text-davinci-003,text-curie-001,text-babbage-001,text-ada-001,text-embedding-ada-002,code-davinci-002,code-cushman-001,content-filter-alpha',
                        ],
                        // 'prompt' => 'string|array',
                        'prompt' => 'string',
                        'suffix' => 'nullable|string',
                        'max_tokens' => 'integer',
                        'temperature' => 'numeric',
                        'top_p' => 'numeric',
                        'n' => 'integer',
                        'stream' => 'bool',
                        'logprobs' => 'nullable|integer',
                        'echo' => 'bool',
                        // 'stop' => 'nullable|string|array',
                        'stop' => 'nullable|string',
                        'presence_penalty' => 'numeric',
                        'frequency_penalty' => 'numeric',
                        'best_of' => 'integer',
                        'logit_bias' => 'array', // map
                        'user' => 'string|uuid',
                    ]
                )
            ),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Authorization: Bearer {$this->config['api_key']}",
            ],
            // CURLOPT_TCP_KEEPALIVE => true, // 开启心跳检测
            // CURLOPT_TCP_KEEPIDLE => 10, // 空闲10秒检测一次
            // CURLOPT_TCP_KEEPINTVL => 10, // 每隔10秒检测一次
        ];

        $writer and $options[CURLOPT_WRITEFUNCTION] = static function (object $ch, string $data) use ($writer) {
            $writer($data, $ch);

            return strlen($data); // 必须返回接收到的数据的长度，否则会断开连接。
        };

        curl_setopt_array($curl = curl_init(), $options);

        $response = curl_exec($curl);
        // dump(curl_error($curl), curl_errno($curl), curl_getinfo($curl));
        curl_close($curl);

        return collect($response);
    }

    /**
     * {@inheritDoc}
     */
    protected function validateConfig(array $config): array
    {
        return array_merge(
            [
                'http_options' => [],
                'base_url' => 'https://api.openai.com/v1',
                'retry' => [
                    'times' => 1,
                    'sleepMilliseconds' => 100,
                    'when' => static fn (\Throwable $throwable) => $throwable instanceof ConnectionException,
                    'throw' => true,
                ],
            ],
            $this->validate($config, [
                'http_options' => 'array',
                'base_url' => 'string',
                'api_key' => 'required|string',
                'retry' => 'array',
                'retry.times' => 'integer',
                'retry.sleepMilliseconds' => 'integer',
                'retry.when' => 'nullable',
                'retry.throw' => 'bool',
            ])
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function buildPendingRequest(array $config): PendingRequest
    {
        return Http::baseUrl($config['base_url'])
            ->throw()
            ->asJson()
            ->withToken($config['api_key'])
            ->withOptions($config['http_options'])
            ->retry(
                $config['retry']['times'],
                $config['retry']['sleepMilliseconds'],
                $config['retry']['when'],
                $config['retry']['throw']
            );
    }
}
