<?php

namespace App\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * @see https://beta.openai.com/docs/api-reference/introduction
 */
class OpenAI extends FoundationSdk
{
    public function completions(array $data): Response
    {
        return $this
            ->pendingRequest
            ->post('completions', $this->validateData(
                $data,
                [
                    'model' => [
                        'required',
                        'string',
                        'in:text-davinci-003,text-curie-001,text-babbage-001,text-ada-001,code-davinci-002,code-cushman-001,content-filter-alpha',
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

    /**
     * {@inheritDoc}
     */
    protected function validateConfig(array $config): array
    {
        return array_merge(
            [
                'http_options' => [],
                'base_url' => 'https://api.openai.com/v1',
            ],
            $this->validateData($config, [
                'http_options' => 'array',
                'base_url' => 'string',
                'api_key' => 'required|string',
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
            ->withOptions($config['http_options']);
    }
}
