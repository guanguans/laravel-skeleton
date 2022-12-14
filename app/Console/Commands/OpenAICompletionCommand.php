<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Orhanerday\OpenAi\OpenAi;

class OpenAICompletionCommand extends Command
{
    protected $signature = 'openai:completion';

    protected $description = 'Openai completion.';

    public function handle()
    {
        collect()
            ->tap(function () {
                if (! class_exists(OpenAi::class)) {
                    $this->warn('The "orhanerday/open-ai" package is required to use this command.');
                    $this->warn('You can install it with "composer require orhanerday/open-ai".');
                    exit(self::INVALID);
                }
            })
            ->tap(function () use (&$prompt) {
                for (; ;) {
                    $prompt = $this->ask('请输入提示信息.');
                    if (filled($prompt)) {
                        break;
                    }
                }
            })
            ->pipe(static function () use ($prompt): Collection {
                /** @var array{id: string, object: string, created: int, model: string, choices: array<int, array{text: string, index: int, logprobs: array{tokens: array<int, string>, token_logprobs: array<int, float>, top_logprobs: array<int, string>|null, text_offset: array<int, int>}|null, finish_reason: string}>, usage: array{prompt_tokens: int, completion_tokens: int, total_tokens: int}} $completion */
                $completion = (new OpenAi(config('services.openai_api_key')))
                    ->completion([
                        'model' => 'text-davinci-003',
                        'prompt' => $prompt,
                        'max_tokens' => 4000,
                        'temperature' => 0.9,
                        'top_p' => 1,
                        'n' => 1,
                        'presence_penalty' => 0.6,
                        'frequency_penalty' => 0,
                        'stream' => false,
                        'logprobs' => null,
                        'stop' => '\n',
                    ]);

                return collect(json_decode($completion, true, 512, JSON_THROW_ON_ERROR));
            })
            ->tap(function (Collection $collection) {
                if ($error = $collection->get('error')) {
                    $this->error($error['message']);
                    exit(self::FAILURE);
                }
            })
            ->tap(function (Collection $collection) {
                if (! $text = Arr::get($collection, 'choices.0.text')) {
                    $this->warn($collection->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                }

                $this->info($text);
            })
            ->tap(fn () => $this->call(self::class));
    }
}
