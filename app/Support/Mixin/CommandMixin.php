<?php

/** @noinspection PhpMethodParametersCountMismatchInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Mixin;

use App\Support\Attribute\Mixin;
use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Log\Logger;
use Illuminate\Process\PendingProcess;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * @see https://github.com/nunomaduro/laravel-console-task
 *
 * @mixin \Illuminate\Console\Command
 *
 * @method toSymfonyProcess(null|array|string $command)
 */
#[Mixin(Command::class)]
final class CommandMixin
{
    public function consoleLogger(): \Closure
    {
        return fn (
            array $verbosityLevelMap = [],
            array $formatLevelMap = [],
            ?OutputInterface $output = null,
        ): Logger => new Logger(
            new ConsoleLogger($output ?? $this->output, $verbosityLevelMap, $formatLevelMap),
            resolve(Dispatcher::class)
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
            $process = $this->processHelperRun($cmd, $error, $callback, $verbosity, $output);
            \assert($process instanceof Process);

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

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

            $helper = $this->getHelper('process');
            \assert($helper instanceof ProcessHelper);

            return $helper->run($output ?? $this->output, $cmd, $error, $callback, $verbosity);
        };
    }
}
