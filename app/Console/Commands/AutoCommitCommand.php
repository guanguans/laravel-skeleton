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
        {--no-edit : Do not force edit commit message}
        {--only-dry-run : Output the generated commit message but don't create a commit}
        {--l|lang=english : Try to generate commit message in this language}
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

        self::task(
            'Getting commit message',
            function () use (&$commitMessage, $stagedDiff, $openAI) {
                $this->newLine();

                $openAI
                    ->completions(
                        [
                            'model' => 'text-davinci-003',
                            'prompt' => $this->getPromptOfOpenAI($stagedDiff),
                            'max_tokens' => 62,
                            'temperature' => 0.0,
                            'top_p' => 1.0,
                            'stream' => true,
                            'user' => Str::uuid()->toString(),
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
                                    $this->output->write("<info>$text</info>");
                                });
                        }
                    );

                $this->newLine(2);

                return true;
            },
            'generating...'
        );

        if ($this->option('only-dry-run')) {
            return self::SUCCESS;
        }

        self::task(
            'Committing message',
            function () use ($commitMessage) {
                (new Process($this->getCommitCommand($commitMessage)))
                    ->setTty(true)
                    ->setTimeout(null)
                    ->mustRun();

                return true;
            },
            'committing...'
        );

        return self::SUCCESS;
    }

    protected function getCommitCommand(string $commitMessage): array
    {
        $clearedCommitMessage = \str($commitMessage)
            ->trim()
            ->ltrim('Commit message: ')
            ->ltrim('Message: ');

        $command = ['git', 'commit', '--message', $clearedCommitMessage];
        if (! $this->option('no-edit')) {
            $command[] = '--edit';
        }

        return $command;
    }

    protected function getPromptOfOpenAI(string $stagedDiff): string
    {
        $lang = ucfirst($this->option('lang') ?? 'english');

        return <<<prompt
I want you to act as a commit message generator. 
I will provide you with information about the task and the prefix for the task code, 
and I would like you to generate an appropriate commit message using the conventional commit format. 
Do not write any explanations or other words, just reply with the commit message.
In $lang.

$stagedDiff
prompt;
    }
}
