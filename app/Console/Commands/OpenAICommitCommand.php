<?php

namespace App\Console\Commands;

use App\Support\OpenAI;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Symfony\Component\Process\Process;

class OpenAICommitCommand extends Command
{
    protected $signature = '
        openai:commit
        {--no-edit : Do not force edit commit message}
        {--num=3 : The number of commit messages generated}
        {--p|prompt= : Provides a prompt template for generating commit messages}
    ';

    protected $description = 'Automagically generate commit messages with OpenAI.';

    public function handle(OpenAI $openAI)
    {
        $isInsideWorkTreeRevParse = Process::fromShellCommandline('git rev-parse --is-inside-work-tree')
            ->mustRun()
            ->getOutput();
        if (! \str($isInsideWorkTreeRevParse)->rtrim()->is('true')) {
            $this->error('It looks like you are not in a git repository.');
            $this->error('Please run this command from the root of a git repository, or initialize one using `git init`.');

            return self::FAILURE;
        }

        $stagedDiff = Process::fromShellCommandline('git diff --staged')
            ->mustRun()
            ->getOutput();
        if (empty($stagedDiff)) {
            $this->error('There are no staged files to commit. Try running `git add` to stage some files.');

            return self::FAILURE;
        }

        self::task(
            'Getting commit message',
            function () use (&$commitMessages, $stagedDiff, $openAI) {
                $openAI
                    ->completions(
                        [
                            'model' => 'text-davinci-003',
                            'prompt' => $this->getPromptOfOpenAI($stagedDiff),
                            'max_tokens' => 1000,
                            'temperature' => 0.0,
                            'top_p' => 1.0,
                            'stream' => true,
                            'user' => Str::uuid()->toString(),
                        ],
                        function (string $data) use (&$commitMessages) {
                            \str($data)
                                ->replaceFirst('data: ', '')
                                ->rtrim()
                                ->tap(function (Stringable $stringable) use (&$commitMessages) {
                                    if ($stringable->startsWith('[DONE]')) {
                                        return;
                                    }

                                    $text = Arr::get(json_decode($stringable, true), 'choices.0.text', '');
                                    $commitMessages .= $text;
                                    $this->output->write("<info>$text</info>");
                                });
                        }
                    );

                $this->newLine(2);

                return true;
            },
            'generating...'
        );

        if (! $this->confirm('Do you want to commit message?')) {
            return self::SUCCESS;
        }

        self::task(
            'Committing message',
            function () use ($commitMessages) {
                (new Process($this->getCommitCommand($commitMessages)))
                    ->setTty(true)
                    ->setTimeout(null)
                    ->mustRun();

                return true;
            },
            'committing...'
        );

        return self::SUCCESS;
    }

    protected function getCommitCommand(string $commitMessages): array
    {
        $clearedCommitMessage = \str($commitMessages)
            ->trim()
            ->replace(/** @lang PhpRegExp */ '/(\r\n|\n|\r|\'|"|`|)/gm', '')
            ->trim();

        $command = ['git', 'commit', '--message', $clearedCommitMessage];
        if (! $this->option('no-edit')) {
            $command[] = '--edit';
        }

        return $command;
    }

    protected function getPromptOfOpenAI(string $stagedDiff): string
    {
        $prompt = $this->option('prompt') ?: <<<'prompt'
I want you to act as a commit message generator. 
I will provide you with information about the task and the prefix for the task code, 
and I would like you to generate an appropriate commit message using the conventional commit format. 
Do not write any explanations or other words, just reply with the commit message.

```
{{diff}}
```
prompt;

        $prompt = $this->option('prompt') ?: $this->promtps()[2]['prompt'];

        return \str($prompt)
            ->replace(['{{diff}}', '{{num}}'], [$stagedDiff, $this->option('num') ?: 3])
            ->toString();
    }

    /**
     * @see https://github.com/ahmetkca/CommitAI
     * @see https://github.com/shanginn/git-aicommit
     *
     * @return array[]
     */
    protected function promtps(): array
    {
        return [
            [
                'id' => 1,
                'prompt' => <<<'prompt'
Here is the output of the `git diff`:
```
{{diff}}
```

Summarize the changes made in the given `git diff` output in a clear and concise commit message that accurately reflects the modifications made to the code-base.
Use best practices for writing commit messages, and be sure to follow the conventional commit format. Use imperative mood, and be sure to keep the commit message under 50 characters.

Please provide a response in the form of a valid JSON object, containing {{num}} commit messages in the following format:
{
    "commit_messages": [
        "commit message 1",
        "commit message 2",
        ...
        "commit message n",
    ]
}
The response MUST ONLY contains the JSON object, and no other text. For example, if you print the JSON object, do NOT include "Output:", "Response:" or anything similar to those two before it.
prompt
            ],
            [
                'id' => 2,
                'prompt' => <<<'prompt'
Here is the output of the `git diff`:
```
{{diff}}
```
Craft a clear and concise commit message that accurately reflects the changes made in the given git diff output, using best practices for commit message writing and following the conventional commit format. Use imperative mood and keep the message under 50 characters.
Please provide a response in the form of a valid JSON object, containing {{num}} commit messages in the following format:
{
    "commit_messages": [
        "commit message 1",
        "commit message 2",
        ...
        "commit message n",
    ]
}
The response MUST ONLY contains the JSON object, and no other text. For example, if you print the JSON object, do NOT include "Output:", "Response:" or anything similar to those two before it.
prompt
            ],
            [
                'id' => 3,
                'prompt' => <<<'prompt'
Here is the output of the `git diff`:
```
{{diff}}
```
Generate a commit message that accurately summarizes the changes made in the given git diff output, following best practices for writing commit messages and the conventional commit format. Use imperative mood and aim for a message under 50 characters in length.
Please provide a response in the form of a valid JSON object, containing {{num}} commit messages in the following format:
{
    "commit_messages": [
        "commit message 1",
        "commit message 2",
        ...
        "commit message n",
    ]
}
The response MUST ONLY contains the JSON object, and no other text. For example, if you print the JSON object, do NOT include "Output:", "Response:" or anything similar to those two before it.
prompt
            ],
            [
                'id' => 4,
                'prompt' => <<<'prompt'
Here is the output of the `git diff`:
```
{{diff}}
```
Here are some best practices for writing commit messages:
- Write clear, concise, and descriptive messages that explain the changes made in the commit.
- Use the present tense and active voice in the message, for example, "Fix bug" instead of "Fixed bug."
- Use the imperative mood, which gives the message a sense of command, e.g. "Add feature" instead of "Added feature"
- Limit the subject line to 72 characters or less.
- Capitalize the subject line.
- Do not end the subject line with a period.
- Limit the body of the message to 256 characters or less.
- Use a blank line between the subject and the body of the message.
- Use the body of the message to provide additional context or explain the reasoning behind the changes.
- Avoid using general terms like "update" or "change" in the subject line, be specific about what was updated or changed.
- Explain, What was done at a glance in the subject line, and provide additional context in the body of the message.
- Why the change was necessary in the body of the message.
- The details about what was done in the body of the message.
- Any useful details concerning the change in the body of the message.
- Use a hyphen (-) for the bullet points in the body of the message.
Write {{num}} commit messages that accurately summarizes the changes made in the given `git diff` output, following the best practices listed above.
Please provide a response in the form of a valid JSON object and do not include "Output:", "Response:" or anything similar to those two before it, in the following format:
{
    "commit_messages": [
        {
            "id": 1,
            "subject": "<type>(<scope>): <subject>",
            "body": "<BODY (bullet points)>"
        },
        {
            "id": 2,
            "subject": "<type>(<scope>): <subject>",
            "body": "<BODY (bullet points)>"
        },
        ...
        {
            "id": n,
            "subject": "<type>(<scope>): <subject>",
            "body": "<BODY (bullet points)>"
        }
    ]
}
prompt
            ],
        ];
    }
}
