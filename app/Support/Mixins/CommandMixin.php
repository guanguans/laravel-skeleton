<?php

/** @noinspection PhpMethodParametersCountMismatchInspection */

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Log\Logger;
use Illuminate\Process\PendingProcess;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * @mixin \Illuminate\Console\Command
 *
 * @see https://github.com/nunomaduro/laravel-console-task
 */
#[Mixin(Command::class)]
class CommandMixin
{
    /**
     * ```php
     * $this->task('Successful task.', function (){
     *     return true;
     * });
     *
     * $this->task('Failed task.', function (){
     *     return false;
     * });
     *
     * // Specify a 3rd parameter for a custom loading message
     * // Default is `loading...`
     * $this->task('Long task.', function (){
     *     sleep(3);
     *
     *     return true;
     * }, 'sleeping...');
     * ```
     */
    public function task(): callable
    {
        /*
         * Performs the given task, outputs and
         * returns the result.
         *
         * @param  string $title
         * @param  callable|null $task
         *
         * @return bool With the result of the task.
         */
        return function (string $title, $task = null, $loadingText = 'loading...'): bool {
            $this->output->write("$title: <comment>{$loadingText}</comment>");

            if (null === $task) {
                $result = true;
            } else {
                try {
                    $result = false !== $task();
                } catch (\Throwable $taskException) {
                    $result = false;
                }
            }

            if ($this->output->isDecorated()) { // Determines if we can use escape sequences
                // Move the cursor to the beginning of the line
                $this->output->write("\x0D");

                // Erase the line
                $this->output->write("\x1B[2K");
            } else {
                $this->output->writeln(''); // Make sure we first close the previous line
            }

            $this->output->writeln(
                "$title: ".($result ? '<info>✔</info>' : '<error>failed</error>')
            );

            throw_if(isset($taskException), $taskException);

            return $result;
        };
    }

    public function consoleLogger(): \Closure
    {
        return fn (
            array $verbosityLevelMap = [],
            array $formatLevelMap = [],
            ?OutputInterface $output = null,
        ): Logger => new Logger(
            new ConsoleLogger($output ?? $this->output, $verbosityLevelMap, $formatLevelMap),
            app(Dispatcher::class)
        );
    }

    public function processHelperMustRun(): \Closure
    {
        return function (
            array|PendingProcess|Process|string $cmd,
            ?string $error = null,
            ?callable $callback = null,
            int $verbosity = OutputInterface::VERBOSITY_VERY_VERBOSE,
            ?OutputInterface $output = null,
        ): Process {
            /** @var Process $process */
            $process = $this->processHelperRun($cmd, $error, $callback, $verbosity, $output);
            throw_unless($process->isSuccessful(), ProcessFailedException::class, $process);

            return $process;
        };
    }

    public function processHelperRun(): \Closure
    {
        return function (
            array|PendingProcess|Process|string $cmd,
            ?string $error = null,
            ?callable $callback = null,
            int $verbosity = OutputInterface::VERBOSITY_VERY_VERBOSE,
            ?OutputInterface $output = null,
        ): Process {
            if (\is_string($cmd)) {
                $cmd = Process::fromShellCommandline($cmd);
            }

            if ($cmd instanceof PendingProcess) {
                $cmd = (fn (): Process => $this->toSymfonyProcess(null))->call($cmd);
            }

            /** @var \Symfony\Component\Console\Helper\ProcessHelper $helper */
            $helper = $this->getHelper('process');

            return $helper->run($output ?? $this->output, $cmd, $error, $callback, $verbosity);
        };
    }
}
