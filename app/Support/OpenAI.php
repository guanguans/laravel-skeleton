<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support;

use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Psr\Http\Message\ResponseInterface;

/**
 * @see https://beta.openai.com/docs/api-reference/introduction
 */
final class OpenAI extends FoundationSdk
{
    public static function hydrateData(string $data): string
    {
        return (string) str($data)->whenStartsWith($prefix = 'data: ', static fn (Stringable $data): Stringable => $data->replaceFirst($prefix, ''));
    }

    /**
     * ```ok
     * {
     *     "id": "cmpl-6n1qMNWwuF5SYBcS4Nev5sr4ACpEB",
     *     "object": "text_completion",
     *     "created": 1677143178,
     *     "model": "text-davinci-003",
     *     "choices": [
     *         {
     *             "text": "\n\n[\n    {\n        \"id\": 1,\n        \"subject\": \"Fix(OpenAIGenerator): Debugging output\",\n        \"body\": \"- Add var_dump() for debugging output\\n- Add var_dump() for stream response\"\n    },\n    {\n        \"id\": 2,\n        \"subject\": \"Refactor(OpenAIGenerator): Error handling\",\n        \"body\": \"- Check for error response in JSON\\n- Handle error response\"\n    },\n    {\n        \"id\": 3,\n        \"subject\": \"Docs(OpenAIGenerator): Update documentation\",\n        \"body\": \"- Update documentation for OpenAIGenerator class\"\n    }\n]",
     *             "index": 0,
     *             "logprobs": null,
     *             "finish_reason": "stop"
     *         }
     *     ],
     *     "usage": {
     *         "prompt_tokens": 749,
     *         "completion_tokens": 159,
     *         "total_tokens": 908
     *     }
     * }
     * ```.
     *
     * ```stream ok
     * data: {"id": "cmpl-6n1mYrlWTmE9184S4pajlIx6JITEu", "object": "text_completion", "created": 1677142942, "choices": [{"text": "", "index": 0, "logprobs": null, "finish_reason": "stop"}], "model": "text-davinci-003"}
     *
     * data: [DONE]
     *
     * ```
     *
     * ```error
     * {
     *     "error": {
     *         "message": "Incorrect API key provided: sk-........ You can find your API key at https://platform.openai.com/account/api-keys.",
     *         "type": "invalid_request_error",
     *         "param": null,
     *         "code": "invalid_api_key"
     *     }
     * }
     * ```
     */
    public function completions(array $parameters, ?callable $writer = null): Response
    {
        return $this->completion(
            'completions',
            $parameters,
            [
                'model' => [
                    'required',
                    'string',
                    'in:text-davinci-003,text-davinci-002,text-curie-001,text-babbage-001,text-ada-001,davinci,curie,babbage,ada',
                ],
                // 'prompt' => 'string|array',
                'prompt' => 'string',
                'suffix' => 'nullable|string',
                'max_tokens' => 'integer',
                'temperature' => 'numeric|between:0,2',
                'top_p' => 'numeric|between:0,1',
                'n' => 'integer|min:1',
                'stream' => 'bool',
                'logprobs' => 'nullable|integer|between:0,5',
                'echo' => 'bool',
                // 'stop' => 'nullable|string|array',
                'stop' => 'nullable|string',
                'presence_penalty' => 'numeric|between:-2,2',
                'frequency_penalty' => 'numeric|between:-2,2',
                'best_of' => 'integer|min:1',
                'logit_bias' => 'array', // map
                'user' => 'string|uuid',
            ],
            $writer
        );
    }

    /**
     * ```php
     * [
     *     'id' => 'chatcmpl-6pqDoRwRGQAlRvJnesR9QMG9rxpyK',
     *     'object' => 'chat.completion',
     *     'created' => 1677813488,
     *     'model' => 'gpt-3.5-turbo-0301',
     *     'usage' => [
     *         'prompt_tokens' => 8,
     *         'completion_tokens' => 16,
     *         'total_tokens' => 24,
     *     ],
     *     'choices' => [
     *         [
     *             'message' => [
     *                 'role' => 'assistant',
     *                 'content' => 'PHP (Hypertext Preprocessor) is a server-side scripting language used',
     *             ],
     *             'finish_reason' => 'length',
     *             'index' => 0,
     *         ],
     *     ],
     * ];
     * ```.
     *
     * ```stream
     * data: {"id":"chatcmpl-6pqQB0NVBCjNcs6aPeFUi4gy1pCoj","object":"chat.completion.chunk","created":1677814255,"model":"gpt-3.5-turbo-0301","choices":[{"delta":{"content":" used"},"index":0,"finish_reason":null}]}
     *
     * data: {"id":"chatcmpl-6pqQB0NVBCjNcs6aPeFUi4gy1pCoj","object":"chat.completion.chunk","created":1677814255,"model":"gpt-3.5-turbo-0301","choices":[{"delta":{},"index":0,"finish_reason":"length"}]}
     *
     * data: [DONE]
     * ```
     */
    public function chatCompletions(array $parameters, ?callable $writer = null): Response
    {
        return $this->completion(
            'chat/completions',
            $parameters,
            [
                'model' => [
                    'required',
                    'string',
                    'in:gpt-4,gpt-4-0314,gpt-4-32k,gpt-4-32k-0314,gpt-3.5-turbo,gpt-3.5-turbo-0301',
                ],
                'messages' => 'required|array',
                'temperature' => 'numeric|between:0,2',
                'top_p' => 'numeric|between:0,1',
                'n' => 'integer|min:1',
                'stream' => 'bool',
                // 'stop' => 'nullable|string|array',
                'stop' => 'nullable|string',
                'max_tokens' => 'integer',
                'presence_penalty' => 'numeric|between:-2,2',
                'frequency_penalty' => 'numeric|between:-2,2',
                'logit_bias' => 'array', // map
                'user' => 'string|uuid',
            ],
            $writer
        );
    }

    /**
     * ```php
     * [
     *     'ada',
     *     'ada-code-search-code',
     *     'ada-code-search-text',
     *     'ada-search-document',
     *     'ada-search-query',
     *     'ada-similarity',
     *     'ada:2020-05-03',
     *     'audio-transcribe-deprecated',
     *     'babbage',
     *     'babbage-code-search-code',
     *     'babbage-code-search-text',
     *     'babbage-search-document',
     *     'babbage-search-query',
     *     'babbage-similarity',
     *     'babbage:2020-05-03',
     *     'code-cushman-001',
     *     'code-davinci-002',
     *     'code-davinci-edit-001',
     *     'code-search-ada-code-001',
     *     'code-search-ada-text-001',
     *     'code-search-babbage-code-001',
     *     'code-search-babbage-text-001',
     *     'curie',
     *     'curie-instruct-beta',
     *     'curie-search-document',
     *     'curie-search-query',
     *     'curie-similarity',
     *     'curie:2020-05-03',
     *     'cushman:2020-05-03',
     *     'davinci',
     *     'davinci-if:3.0.0',
     *     'davinci-instruct-beta',
     *     'davinci-instruct-beta:2.0.0',
     *     'davinci-search-document',
     *     'davinci-search-query',
     *     'davinci-similarity',
     *     'davinci:2020-05-03',
     *     'gpt-3.5-turbo',
     *     'gpt-3.5-turbo-0301',
     *     'if-curie-v2',
     *     'if-davinci-v2',
     *     'if-davinci:3.0.0',
     *     'text-ada-001',
     *     'text-ada:001',
     *     'text-babbage-001',
     *     'text-babbage:001',
     *     'text-curie-001',
     *     'text-curie:001',
     *     'text-davinci-001',
     *     'text-davinci-002',
     *     'text-davinci-003',
     *     'text-davinci-edit-001',
     *     'text-davinci-insert-001',
     *     'text-davinci-insert-002',
     *     'text-davinci:001',
     *     'text-embedding-ada-002',
     *     'text-search-ada-doc-001',
     *     'text-search-ada-query-001',
     *     'text-search-babbage-doc-001',
     *     'text-search-babbage-query-001',
     *     'text-search-curie-doc-001',
     *     'text-search-curie-query-001',
     *     'text-search-davinci-doc-001',
     *     'text-search-davinci-query-001',
     *     'text-similarity-ada-001',
     *     'text-similarity-babbage-001',
     *     'text-similarity-curie-001',
     *     'text-similarity-davinci-001',
     *     'whisper-1',
     * ];
     * ```.
     */
    public function models(): Response
    {
        return $this->clonePendingRequest()->get('models')->throw();
    }

    public function completionsByCurl(array $data, ?callable $writer = null): Collection
    {
        $options = [
            \CURLOPT_URL => "{$this->config['base_url']}/completions",
            \CURLOPT_RETURNTRANSFER => true,
            \CURLOPT_HTTP_VERSION => \CURL_HTTP_VERSION_1_1,
            \CURLOPT_CUSTOMREQUEST => 'POST',
            \CURLOPT_POSTFIELDS => json_encode(
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
            \CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Authorization: Bearer {$this->config['api_key']}",
            ],
            // CURLOPT_TCP_KEEPALIVE => true, // 开启心跳检测
            // CURLOPT_TCP_KEEPIDLE => 10, // 空闲10秒检测一次
            // CURLOPT_TCP_KEEPINTVL => 10, // 每隔10秒检测一次
        ];

        $writer and $options[\CURLOPT_WRITEFUNCTION] = static function (object $ch, string $data) use ($writer): int {
            $writer($data, $ch);

            return \strlen($data); // 必须返回接收到的数据的长度，否则会断开连接。
        };

        curl_setopt_array($curl = curl_init(), $options);

        $response = curl_exec($curl);
        // dump(curl_error($curl), curl_errno($curl), curl_getinfo($curl));
        curl_close($curl);

        return collect($response);
    }

    #[\Override]
    protected function validateConfig(array $config): array
    {
        return array_replace_recursive(
            [
                'http_options' => [
                    // \GuzzleHttp\RequestOptions::CONNECT_TIMEOUT => 30,
                    // \GuzzleHttp\RequestOptions::TIMEOUT => 180,
                ],
                'retry' => [
                    // 'times' => 1,
                    // 'sleep' => 1000,
                    // 'when' => static function (\Exception $exception): bool {
                    //     return $exception instanceof \Illuminate\Http\Client\ConnectionException;
                    // },
                    // // 'throw' => true,
                ],
                'base_url' => 'https://api.openai.com/v1',
            ],
            $this->validate($config, [
                'http_options' => 'array',
                'retry' => 'array',
                'retry.times' => 'integer',
                'retry.sleep' => 'integer',
                'retry.when' => 'nullable',
                // 'retry.throw' => 'bool',
                'base_url' => 'string',
                'api_key' => 'required|string',
            ])
        );
    }

    #[\Override]
    protected function buildPendingRequest(array $config): PendingRequest
    {
        return parent::buildPendingRequest($config)
            ->baseUrl($config['base_url'])
            ->asJson()
            ->withToken($config['api_key'])
            // ->dump()
            // ->throw()
            // ->retry(
            //     $config['retry']['times'],
            //     $config['retry']['sleep'],
            //     $config['retry']['when']
            //     // $config['retry']['throw']
            // )
            ->withOptions($config['http_options']);
    }

    private function completion(string $url, array $parameters, array $rules, ?callable $writer = null, array $messages = [], array $customAttributes = []): Response
    {
        $response = $this
            ->clonePendingRequest()
            ->when(
                ($parameters['stream'] ?? false) && \is_callable($writer),
                static function (PendingRequest $pendingRequest) use ($writer, &$rowData): PendingRequest {
                    return $pendingRequest->withOptions([
                        'curl' => [
                            \CURLOPT_WRITEFUNCTION => static function ($ch, string $data) use ($writer, &$rowData): int {
                                if (!str($data)->startsWith('data: [DONE]')) {
                                    $rowData = $data;
                                }

                                $writer($data, $ch);

                                return \strlen($data);
                            },
                        ],
                    ]);
                }
            )
            // ->withMiddleware(
            //     Middleware::mapResponse(static function (ResponseInterface $response): ResponseInterface {
            //         $contents = $response->getBody()->getContents();
            //
            //         // $parameters['stream'] === true && $writer === null
            //         if ($contents && ! \str($contents)->isJson()) {
            //             $data = \str($contents)
            //                 ->explode("\n\n")
            //                 ->reverse()
            //                 ->skip(2)
            //                 ->reverse()
            //                 ->map(static function (string $rowData): array {
            //                     return json_decode(self::hydrateData($rowData), true);
            //                 })
            //                 ->reduce(static function (array $data, array $rowData): array {
            //                     if (empty($data)) {
            //                         return $rowData;
            //                     }
            //
            //                     foreach ($data['choices'] as $index => $choice) {
            //                         $data['choices'][$index]['text'] .= $rowData['choices'][$index]['text'];
            //                     }
            //
            //                     return $data;
            //                 }, []);
            //
            //             return $response->withBody(Utils::streamFor(json_encode($data)));
            //         }
            //
            //         return $response;
            //     })
            // )
            ->post($url, $this->validate($parameters, $rules, $messages, $customAttributes));
        // ->onError(function (Response $response) use ($rowData) {
        //     if ($rowData && empty($response->body())) {
        //         (function (Response $response) use ($rowData): void {
        //             $this->response = $response->toPsrResponse()->withBody(
        //                 Utils::streamFor(OpenAI::hydrateData($rowData))
        //             );
        //         })->call($response, $response);
        //     }
        // })

        if ($rowData && empty($response->body())) {
            $response = new Response(
                $response->toPsrResponse()->withBody(Utils::streamFor(self::hydrateData($rowData)))
            );
        }

        return $response->throw();
    }
}
