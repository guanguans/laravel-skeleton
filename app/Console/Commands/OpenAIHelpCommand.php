<?php

namespace App\Console\Commands;

use App\Support\OpenAI;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class OpenAIHelpCommand extends Command
{
    protected $signature = 'openai:help';

    protected $description = 'OpenAI help.';

    public function handle(OpenAI $openAI): void
    {
        for (; ;) {
            $prompt = $this->ask('请输入提示信息');
            if (filled($prompt)) {
                break;
            }
        }

        $openAI
            ->completions(
                [
                    'model' => 'text-davinci-003',
                    'prompt' => $prompt,
                    'suffix' => null,
                    // 'max_tokens' => 4000,
                    'temperature' => 0,
                    'top_p' => 1,
                    'n' => 1,
                    'stream' => true,
                    'logprobs' => null,
                    'echo' => false,
                    'stop' => null,
                    'presence_penalty' => 0,
                    'frequency_penalty' => 0,
                    'best_of' => 1,
                    // 'logit_bias' => null,
                    'user' => Str::uuid()->toString(),
                ],
                function (string $data): void {
                    str($data)
                        ->replaceFirst('data: ', '')
                        ->rtrim()
                        ->tap(function (Stringable $stringable): void {
                            if ($stringable->startsWith('[DONE]')) {
                                return;
                            }

                            $text = Arr::get(json_decode($stringable, true), 'choices.0.text', '');
                            $this->output->write($text);
                        });
                }
            )
            ->collect()
            ->tap(fn () => $this->call(self::class));
    }
}
