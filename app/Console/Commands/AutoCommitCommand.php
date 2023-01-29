<?php

namespace App\Console\Commands;

use App\Support\OpenAI;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Symfony\Component\Process\Process;

class AutoCommitCommand extends Command
{
    protected $signature = "
        auto-commit
        {--no-edit : Do not force edit commits}
        {--only-dry-run : Output the generated message, but don't create a commit}
    ";

    protected $description = 'Automagically generate commit messages.';

    public function handle(OpenAI $openAI)
    {
        $stagedDiff = Process::fromShellCommandline('git diff --staged')
            ->mustRun()
            ->getOutput();
        if (empty($stagedDiff)) {
            $this->error('There are no staged files to commit. Try running `git add` to stage some files.');

            return self::FAILURE;
        }

        $isInsideWorkTreeRevParse = Process::fromShellCommandline('git rev-parse --is-inside-work-tree')
            ->mustRun()
            ->getOutput();
        if (trim($isInsideWorkTreeRevParse) !== 'true') {
            $this->error(
                'It looks like you are not in a git repository. Please run this command from the root of a git repository, or initialize one using `git init`.'
            );

            return self::FAILURE;
        }

        $openAI
            ->completions(
                [
                    'model' => 'code-davinci-002',
                    'prompt' => $this->getPromptOfOpenAI($stagedDiff),
                    'max_tokens' => 62,
                    'temperature' => 0,
                    // 'top_p' => 1,
                    'stream' => true,
                    // 'user' => Str::uuid()->toString(),
                ],
                function (string $data) use (&$commitMessage) {
                    \str($data)
                        ->replaceFirst('data: ', '')
                        ->rtrim()
                        ->tap(function (Stringable $stringable) use (&$commitMessage) {
                            if ($stringable->startsWith('[DONE]')) {
                                return;
                            }

                            $text = Arr::get(json_decode($stringable, true), 'choices.0.text', '');
                            $commitMessage .= $text;
                            $this->output->write($text);
                        });
                }
            )
            ->collect()
            ->tap(function () {
                $this->newLine(3);
            });

        if ($this->option('only-dry-run')) {
            return self::SUCCESS;
        }

        (new Process($this->getCommitCommand($commitMessage)))
            ->setTty(true)
            ->setTimeout(null)
            ->mustRun(function ($type, $buffer) {
                $this->output->write($buffer);
            });

        return self::SUCCESS;
    }

    protected function getCommitCommand(string $commitMessage): array
    {
        $command = ['git', 'commit', '--message', $commitMessage];
        if (! $this->option('no-edit')) {
            $command[] = '--edit';
        }

        return $command;
    }

    protected function getPromptOfOpenAI(string $stagedDiff): string
    {
        return sprintf(
            "git diff --staged\\^!\n{%s}\n\n# Write a commit message describing the changes and the reasoning behind them\ngit commit -F- <<EOF",
            $stagedDiff
        );
    }
}
