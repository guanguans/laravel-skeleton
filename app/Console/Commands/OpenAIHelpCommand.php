<?php

namespace App\Console\Commands;

use App\Support\OpenAI;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class OpenAIHelpCommand extends Command
{
    protected $signature = 'openai:help';

    protected $description = 'OpenAI help.';

    public function handle(OpenAI $openAI)
    {
        for (; ;) {
            $prompt = $this->ask('请输入提示信息');
            if (filled($prompt)) {
                break;
            }
        }

        $openAI
            ->completions([
                'model' => 'text-davinci-003',
                'prompt' => $prompt,
                'suffix' => null,
                'max_tokens' => 4000,
                'temperature' => 0,
                'top_p' => 1,
                'n' => 1,
                'stream' => false,
                'logprobs' => null,
                'echo' => false,
                'stop' => null,
                'presence_penalty' => 0,
                'frequency_penalty' => 0,
                'best_of' => 1,
                // 'logit_bias' => null,
                'user' => Str::uuid()->toString(),
            ])
            ->collect()
            ->tap(function (Collection $collection) {
                if (! $text = Arr::get($collection, 'choices.0.text')) {
                    $this->warn($collection->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                }

                $this->line($text);
            })
            ->tap(fn () => $this->call(self::class));
    }
}
