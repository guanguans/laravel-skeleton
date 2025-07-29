<?php

/** @noinspection MissingParentCallInspection */
/** @noinspection PhpConstantNamingConventionInspection */
/** @noinspection PhpDeprecationInspection */
/** @noinspection PhpInternalEntityUsedInspection */
/** @noinspection PhpMissingParentCallCommonInspection */
/** @noinspection SensitiveParameterInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\PhpCsFixer\Fixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FileReader;
use PhpCsFixer\FileRemoval;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ConfigurableFixerTrait;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * @see https://github.com/shufo/blade-formatter
 *
 * @property array{
 *     command: array,
 *     options: array,
 *     cwd: ?string,
 *     env: ?array,
 *     input: ?string,
 *     timeout: ?float
 * } $configuration
 *
 * @method void configureIO(InputInterface $input, OutputInterface $output)
 */
final class BladeFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    use ConfigurableFixerTrait;
    public const string COMMAND = 'command';
    public const string OPTIONS = 'options';
    public const string CWD = 'cwd';
    public const string ENV = 'env';
    public const string INPUT = 'input';
    public const string TIMEOUT = 'timeout';

    #[\Override]
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            $summary = \sprintf('Format a [%s] file.', $this->getShortHeadlineName()),
            [new CodeSample($summary)]
        );
    }

    public static function name(): string
    {
        return (new self)->getName();
    }

    #[\Override]
    public function getName(): string
    {
        return \sprintf('User/%s', $this->getShortName());
    }

    public function getShortHeadlineName(): string
    {
        return str($this->getShortName())->headline()->toString();
    }

    public function getShortName(): string
    {
        return parent::getName();
    }

    #[\Override]
    public function isRisky(): bool
    {
        return true;
    }

    #[\Override]
    public function getPriority(): int
    {
        return \PHP_INT_MAX;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    #[\Override]
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->count() === 1 && $tokens[0]->isGivenKind(\T_INLINE_HTML);
    }

    #[\Override]
    public function supports(\SplFileInfo $file): bool
    {
        return str_ends_with($file->getBasename(), 'blade.php');
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(self::COMMAND, 'The blade-formatter command to run.'))
                ->setAllowedTypes(['string', 'array'])
                ->setDefault('blade-formatter')
                ->setNormalizer(static fn (OptionsResolver $optionsResolver, array|string $value): array => array_map(
                    static fn (string $value): string => str_contains($value, \DIRECTORY_SEPARATOR)
                        ? $value
                        : (new ExecutableFinder)->find($value, $value),
                    (array) $value,
                ))
                ->getOption(),
            (new FixerOptionBuilder(self::OPTIONS, 'The options to pass to the command.'))
                ->setAllowedTypes(['array'])
                ->setDefault([])
                ->getOption(),
            (new FixerOptionBuilder(self::CWD, 'The working directory or null to use the working dir of the current PHP process.'))
                ->setAllowedTypes(['string', 'null'])
                ->setDefault(null)
                ->getOption(),
            (new FixerOptionBuilder(self::ENV, 'The environment variables or null to use the same environment as the current PHP process.'))
                ->setAllowedTypes(['array', 'null'])
                ->setDefault(null)
                ->getOption(),
            (new FixerOptionBuilder(self::INPUT, 'The input as stream resource, scalar or \Traversable, or null for no input.'))
                ->setAllowedTypes(['string', 'null'])
                ->setDefault(null)
                ->getOption(),
            (new FixerOptionBuilder(self::TIMEOUT, 'The timeout in seconds or null to disable.'))
                ->setAllowedTypes(['float', 'int', 'null'])
                ->setDefault(10)
                ->getOption(),
        ]);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     *
     * @throws \Throwable
     */
    #[\Override]
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $process = new Process(
            command: [
                ...$this->configuration[self::COMMAND],
                $finalPath = $this->finalFile($file, $tokens),
                ...$this->options(),
            ],
            cwd: $this->configuration[self::CWD],
            env: $this->configuration[self::ENV],
            input: $this->configuration[self::INPUT],
            timeout: $this->configuration[self::TIMEOUT],
        );
        $process->run();
        $this->debugProcess($process);

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $tokens->setCode(FileReader::createSingleton()->read($finalPath));
    }

    /**
     * @noinspection GlobalVariableUsageInspection
     *
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    private function finalFile(\SplFileInfo $file, Tokens $tokens): string
    {
        $finalFile = (string) $file;

        if (\in_array('--dry-run', $_SERVER['argv'] ?? [], true)) {
            file_put_contents($finalFile = $this->createTemporaryFile(), $tokens->generateCode());
        }

        return $finalFile;
    }

    private function createTemporaryFile(): string
    {
        static $temporaryFile;

        if ($temporaryFile) {
            return $temporaryFile;
        }

        $temporaryFile = tempnam($tempDir = sys_get_temp_dir(), "{$this->getShortName()}_");

        if (!$temporaryFile) {
            throw new \RuntimeException("The temporary file could not be created in the temporary directory [$tempDir].");
        }

        (new FileRemoval)->observe($temporaryFile);

        return $temporaryFile;
    }

    private function options(): array
    {
        return collect(['--write', ...$this->configuration[self::OPTIONS]])->reduce(
            static function (array $options, mixed $value, int|string $key): array {
                \is_string($key) and str_starts_with($key, '-') and $options[] = $key;
                $options[] = $value;

                return $options;
            },
            []
        );
    }

    /**
     * @noinspection DuplicatedCode
     */
    private function debugProcess(Process $process): void
    {
        if (!($symfonyStyle = $this->createSymfonyStyle())->isDebug()) {
            return;
        }

        $symfonyStyle->title("Process debugging information for [{$this->getName()}]");
        $symfonyStyle->warning([
            \sprintf('Command Line: %s', Utils::toString($process->getCommandLine())),
            \sprintf('Exit Code: %s', Utils::toString($process->getExitCode())),
            \sprintf('Exit Code Text: %s', Utils::toString($process->getExitCodeText())),
            \sprintf('Output: %s', Utils::toString($process->getOutput())),
            \sprintf('Error Output: %s', Utils::toString($process->getErrorOutput())),
            \sprintf('Working Directory: %s', Utils::toString($process->getWorkingDirectory())),
            \sprintf('Env: %s', Utils::toString($process->getEnv())),
            \sprintf('Input: %s', Utils::toString($process->getInput())),
            \sprintf('Timeout: %s', Utils::toString($process->getTimeout())),
        ]);
    }

    private function createSymfonyStyle(): SymfonyStyle
    {
        $argvInput = new ArgvInput;
        $consoleOutput = new ConsoleOutput;

        // to configure all -v, -vv, -vvv options without memory-lock to Application run() arguments
        (fn () => $this->configureIO($argvInput, $consoleOutput))->call(new Application);

        return new SymfonyStyle($argvInput, $consoleOutput);
    }
}
