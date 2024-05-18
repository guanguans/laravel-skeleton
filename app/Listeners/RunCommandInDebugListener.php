<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Support\Facades\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * @see https://dev.to/serendipityhq/how-to-debug-any-symfony-command-simply-passing-x-214o
 * @see https://symfony.com/doc/current/components/console/events.html
 */
#[AsEventListener(ConsoleEvents::COMMAND, '__invoke')]
class RunCommandInDebugListener
{
    /**
     * @throws \Throwable
     */
    public function __invoke(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();

        throw_if(false === $command instanceof Command, \RuntimeException::class, 'The command must be an instance of '.Command::class);

        if ($command instanceof HelpCommand) {
            $command = $this->getActualCommandFromHelpCommand($command);
        }

        $command->addOption(
            name: 'xdebug',
            shortcut: 'x',
            mode: InputOption::VALUE_NONE,
            description: 'If passed, the command is re-run setting env variables required by xDebug.',
        );

        if ($command instanceof HelpCommand) {
            return;
        }

        $input = $event->getInput();
        if (
            false === $input instanceof ArgvInput
            || false === $this->isInDebugMode($input)
            || '1' === getenv('XDEBUG_SESSION')
        ) {
            return;
        }

        $output = $event->getOutput();
        $output->writeln('<comment>Relaunching the command with xDebug...</comment>');

        $processResult = Process::forever()->run(
            $this->buildCommandWithXDebugActivated(),
            static fn (string $type, string $buffer) => $output->write($buffer)
        );

        exit($processResult->exitCode());
    }

    /**
     * @throws \Throwable
     */
    private function getActualCommandFromHelpCommand(HelpCommand $command): Command
    {
        $reflection = new \ReflectionClass($command);
        $property = $reflection->getProperty('command');
        $actualCommand = $property->getValue($command);

        throw_if(false === $actualCommand instanceof Command, \RuntimeException::class, 'The command must be an instance of '.Command::class);

        return $actualCommand;
    }

    /**
     * @throws \Throwable
     */
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
     *
     * @throws \Throwable
     */
    private function getTokensFromArgvInput(ArgvInput $input): array
    {
        $reflection = new \ReflectionClass($input);
        $tokensProperty = $reflection->getProperty('tokens');
        $tokens = $tokensProperty->getValue($input);

        throw_if(false === \is_array($tokens), \RuntimeException::class, 'Impossible to get the arguments and options from the command.');

        return $tokens;
    }

    /**
     * @noinspection GlobalVariableUsageInspection
     */
    private function buildCommandWithXDebugActivated(): string
    {
        $serverArgv = $_SERVER['argv'] ?? null;
        throw_if(null === $serverArgv, \RuntimeException::class, 'Impossible to get the arguments and options from the command: the command cannot be relaunched with xDebug.');

        $script = $_SERVER['SCRIPT_NAME'] ?? null;
        throw_if(null === $script, \RuntimeException::class, 'Impossible to get the name of the command: the command cannot be relaunched with xDebug.');

        $phpBinary = PHP_BINARY;
        $args = implode(' ', \array_slice($serverArgv, 1));

        return "XDEBUG_SESSION=1 XDEBUG_MODE=debug XDEBUG_ACTIVATED=1 $phpBinary $script $args --ansi";
    }
}
