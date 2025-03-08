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

namespace App\Listeners;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * @see https://dev.to/serendipityhq/how-to-debug-any-symfony-command-simply-passing-x-214o
 * @see https://symfony.com/doc/current/components/console/events.html
 */
#[AsEventListener(ConsoleEvents::COMMAND, 'configure')]
class RunCommandInDebugModeListener
{
    public function __invoke(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();

        throw_unless($command instanceof Command, \RuntimeException::class, \sprintf('The command must be an instance of %s', Command::class));

        if ($command instanceof HelpCommand) {
            $command = $this->getActualCommandFromHelpCommand($command);
        }

        $command->addOption(
            name: 'xdebug',
            shortcut: 'x',
            mode: InputOption::VALUE_NONE,
            description: 'If passed, the command is re-run setting env variables required by xDebug.',
        );

        $input = $event->getInput();

        if (
            $command instanceof HelpCommand
            || !$input instanceof ArgvInput
            || !config('app.debug')
            || !$this->isInDebugMode($input)
            || '1' === getenv('XDEBUG_SESSION')
        ) {
            return;
        }

        $output = $event->getOutput();
        $output->writeln('<comment>Relaunching the command with xDebug...</comment>');

        $exitCode = (new Process($this->buildCommandWithXDebugActivated()))
            ->setEnv([
                'XDEBUG_SESSION' => '1',
                'XDEBUG_MODE' => 'debug',
                'XDEBUG_ACTIVATED' => '1',
            ])
            ->setTimeout(null)
            ->run(static fn (string $type, string $buffer) => $output->write($buffer));

        exit($exitCode);
    }

    private function getActualCommandFromHelpCommand(HelpCommand $command): Command
    {
        $reflection = new \ReflectionClass($command);
        $property = $reflection->getProperty('command');
        $actualCommand = $property->getValue($command);

        throw_unless($actualCommand instanceof Command, \RuntimeException::class, \sprintf('The command must be an instance of %s', Command::class));

        return $actualCommand;
    }

    private function isInDebugMode(ArgvInput $input): bool
    {
        $tokens = $this->getTokensFromArgvInput($input);

        foreach ($tokens as $token) {
            if ('--xdebug' === $token || '-x' === $token) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    private function getTokensFromArgvInput(ArgvInput $input): array
    {
        $reflection = new \ReflectionClass($input);
        $tokensProperty = $reflection->getProperty('tokens');
        $tokens = $tokensProperty->getValue($input);

        throw_unless(\is_array($tokens), \RuntimeException::class, 'Impossible to get the arguments and options from the command.');

        return $tokens;
    }

    /**
     * @noinspection GlobalVariableUsageInspection
     */
    private function buildCommandWithXDebugActivated(): array
    {
        $serverArgv = $_SERVER['argv'] ?? null;
        throw_if(null === $serverArgv, \RuntimeException::class, 'Impossible to get the arguments and options from the command: the command cannot be relaunched with xDebug.');

        if (!\in_array($ansi = '--ansi', $serverArgv, true)) {
            $serverArgv[] = $ansi;
        }

        $script = $_SERVER['SCRIPT_NAME'] ?? null;
        throw_if(null === $script, \RuntimeException::class, 'Impossible to get the name of the command: the command cannot be relaunched with xDebug.');

        $phpBinary = (new PhpExecutableFinder)->find() ?: \PHP_BINARY;
        $serverArgv = \array_slice($serverArgv, 1);

        return [$phpBinary, $script, ...$serverArgv];
    }
}
