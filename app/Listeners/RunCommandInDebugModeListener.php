<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
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

        if (! $command instanceof Command) {
            throw new \RuntimeException(\sprintf('The command must be an instance of %s', Command::class));
        }

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
            || ! $input instanceof ArgvInput
            || ! config('app.debug')
            || ! $this->isInDebugMode($input)
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

        if (! $actualCommand instanceof Command) {
            throw new \RuntimeException(\sprintf('The command must be an instance of %s', Command::class));
        }

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
     * @return array<string>
     */
    private function getTokensFromArgvInput(ArgvInput $input): array
    {
        $reflection = new \ReflectionClass($input);
        $tokensProperty = $reflection->getProperty('tokens');
        $tokens = $tokensProperty->getValue($input);

        if (! \is_array($tokens)) {
            throw new \RuntimeException('Impossible to get the arguments and options from the command.');
        }

        return $tokens;
    }

    /**
     * @noinspection GlobalVariableUsageInspection
     */
    private function buildCommandWithXDebugActivated(): array
    {
        $serverArgv = $_SERVER['argv'] ?? null;
        if (null === $serverArgv) {
            throw new \RuntimeException('Impossible to get the arguments and options from the command: the command cannot be relaunched with xDebug.');
        }

        if (! \in_array($ansi = '--ansi', $serverArgv, true)) {
            $serverArgv[] = $ansi;
        }

        $script = $_SERVER['SCRIPT_NAME'] ?? null;
        if (null === $script) {
            throw new \RuntimeException('Impossible to get the name of the command: the command cannot be relaunched with xDebug.');
        }

        $phpBinary = (new PhpExecutableFinder)->find() ?: PHP_BINARY;
        $serverArgv = \array_slice($serverArgv, 1);

        return [$phpBinary, $script, ...$serverArgv];
    }
}
