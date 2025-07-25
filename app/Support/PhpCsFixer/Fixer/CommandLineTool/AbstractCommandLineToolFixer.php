<?php

/** @noinspection PhpConstantNamingConventionInspection */
/** @noinspection PhpUnusedAliasInspection */
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

namespace App\Support\PhpCsFixer\Fixer\CommandLineTool;

use App\Support\PhpCsFixer\Fixer\AbstractConfigurableFixer;
use App\Support\PhpCsFixer\Fixer\CommandLineTool\Concerns\PrePathCommand;
use App\Support\PhpCsFixer\Fixer\Concerns\AllowRisky;
use App\Support\PhpCsFixer\Fixer\Concerns\Awarer;
use App\Support\PhpCsFixer\Fixer\Concerns\HighestPriority;
use App\Support\PhpCsFixer\Fixer\Concerns\InlineHtmlCandidate;
use App\Support\PhpCsFixer\Fixer\Concerns\IsDryRun;
use App\Support\PhpCsFixer\Fixer\Concerns\SupportsExtensions;
use App\Support\PhpCsFixer\Fixer\Concerns\SymfonyStyleFactory;
use Illuminate\Support\Str;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\Process;
use function Psl\Filesystem\create_file;
use function Psl\Filesystem\create_temporary_file;

/**
 * @see https://github.com/super-linter/super-linter
 * @see https://marketplace.visualstudio.com/search?term=format&target=VSCode&category=All%20categories&sortBy=Relevance
 * @see https://marketplace.visualstudio.com/search?term=lint&target=VSCode&category=All%20categories&sortBy=Relevance
 * @see https://plugins.jetbrains.com/search?search=format
 * @see https://plugins.jetbrains.com/search?search=lint
 *
 * @property array{
 *     main_command: array,
 *     args: array,
 *     cwd: ?string,
 *     env: ?array,
 *     input: ?string,
 *     timeout: ?float
 * } $configuration
 */
abstract class AbstractCommandLineToolFixer extends AbstractConfigurableFixer
{
    use AllowRisky;
    use Awarer;
    use HighestPriority;
    use InlineHtmlCandidate;
    use IsDryRun;
    use PrePathCommand;
    use SupportsExtensions;
    use SymfonyStyleFactory;
    public const string MAIN_COMMAND = 'main_command';
    public const string ARGS = 'args';
    public const string CWD = 'cwd';
    public const string ENV = 'env';
    public const string INPUT = 'input';
    public const string TIMEOUT = 'timeout';
    private static ?string $temporaryFile = null;

    /**
     * @see \Illuminate\Filesystem\Filesystem::delete()
     */
    public function __destruct()
    {
        if (self::$temporaryFile && unlink(self::$temporaryFile)) {
            clearstatcache(false, self::$temporaryFile);
            self::$temporaryFile = null;
        }
    }

    #[\Override]
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            $summary = \sprintf('Format a file by [%s].', $this->getShortHeadlineName()),
            [new CodeSample($summary)]
        );
    }

    #[\Override]
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([...$this->defaultFixerOptions(), ...$this->fixerOptions()]);
    }

    /**
     * @return list<\PhpCsFixer\FixerConfiguration\FixerOptionInterface>
     */
    protected function defaultFixerOptions(): array
    {
        return [
            (new FixerOptionBuilder(self::MAIN_COMMAND, 'The main command to run the tool (e.g. `dotenv-linter fix`).'))
                ->setAllowedTypes(['array'])
                ->setDefault($this->defaultMainCommand())
                ->getOption(),
            (new FixerOptionBuilder(self::ARGS, 'The args to pass to the main command.'))
                ->setAllowedTypes(['array'])
                ->setDefault($this->defaultArgs())
                ->setNormalizer(static fn (OptionsResolver $optionsResolver, array $value) => collect($value)->reduce(
                    static function (array $carry, mixed $val, int|string $key): array {
                        \is_string($key) and str_starts_with($key, '-') and $carry[] = $key;
                        $carry[] = $val;

                        return $carry;
                    },
                    []
                ))
                ->getOption(),
            (new FixerOptionBuilder(self::CWD, 'The working directory or null to use the working dir of the current PHP process.'))
                ->setAllowedTypes(['string', 'null'])
                ->setDefault($this->defaultCwd())
                ->getOption(),
            (new FixerOptionBuilder(self::ENV, 'The environment variables or null to use the same environment as the current PHP process.'))
                ->setAllowedTypes(['array', 'null'])
                ->setDefault($this->defaultEnv())
                ->getOption(),
            (new FixerOptionBuilder(self::INPUT, 'The input as stream resource, scalar or \Traversable, or null for no input.'))
                ->setAllowedTypes(['string', 'null'])
                ->setDefault($this->defaultInput())
                ->getOption(),
            (new FixerOptionBuilder(self::TIMEOUT, 'The timeout in seconds or null to disable.'))
                ->setAllowedTypes(['float', 'int', 'null'])
                ->setDefault($this->defaultTimeout())
                ->getOption(),
        ];
    }

    /**
     * @return list<\PhpCsFixer\FixerConfiguration\FixerOptionInterface>
     */
    protected function fixerOptions(): array
    {
        return [];
    }

    /**
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    #[\Override]
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $this->setFileAndTokens($file, $tokens);
        $process = $this->createProcess();
        $process->run();

        if ($this->makeOutput()->isDebug()) {
            $this->makeOutput()->title("Process debugging information for [{$this->getName()}]");
            $this->makeOutput()->warning([
                \sprintf('Command Line: %s', $this->escape($process->getCommandLine())),
                \sprintf('Exit Code: %s', $this->escape($process->getExitCode())),
                \sprintf('Exit Code Text: %s', $this->escape($process->getExitCodeText())),
                \sprintf('Output: %s', $this->escape($process->getOutput())),
                \sprintf('Error Output: %s', $this->escape($process->getErrorOutput())),
                \sprintf('Working Directory: %s', $this->escape($process->getWorkingDirectory())),
                \sprintf('Env: %s', $this->escape($process->getEnv())),
                \sprintf('Input: %s', $this->escape($process->getInput())),
                \sprintf('Timeout: %s', $this->escape($process->getTimeout())),
            ]);
        }

        if ($this->isProcessSuccessful($process)) {
            // $tokens[0] = new Token([\TOKEN_PARSE, file_get_contents($this->path())]);
            $tokens->setCode(file_get_contents($this->path()));
        }
    }

    protected function createProcess(): Process
    {
        return new Process(
            command: $this->command(),
            cwd: $this->configuration[self::CWD],
            env: $this->configuration[self::ENV],
            input: $this->configuration[self::INPUT],
            timeout: $this->configuration[self::TIMEOUT],
        );
    }

    protected function path(): string
    {
        static $path;

        if ($path) {
            return $path;
        }

        $path = (string) $this->file;

        if ($this->isDryRun()) {
            file_put_contents($path = $this->createTemporaryFile(), $this->tokens->generateCode());
        }

        return $path;
    }

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    protected function createTemporaryFile(): string
    {
        if (self::$temporaryFile) {
            return self::$temporaryFile;
        }

        $temporaryFile = (new TemporaryDirectory)
            ->deleteWhenDestroyed()
            ->force()
            ->name($this->getShortName())
            ->create()
            ->path(
                str(Str::random())
                    ->remove(\DIRECTORY_SEPARATOR)
                    ->finish('.')
                    ->finish(collect($this->extensions())->random())
                    ->toString()
            );

        // touch($temporaryFile);
        create_file($temporaryFile);

        // return self::$temporaryFile ??= create_temporary_file(null, $this->getSortName());
        return self::$temporaryFile ??= $temporaryFile;
    }

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    protected function escape(mixed $value): string
    {
        return \is_string($value)
            ? $value
            : json_encode(
                $value,
                \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES | \JSON_THROW_ON_ERROR | \JSON_FORCE_OBJECT
            );
    }

    protected function isProcessSuccessful(Process $process): bool
    {
        return $process->isSuccessful();
    }

    abstract protected function defaultMainCommand(): array;

    /**
     * @todo --ansi
     * @todo --color
     * @todo -vvv
     * @todo --debug
     */
    protected function requiredArgs(): array
    {
        return [];
    }

    protected function defaultArgs(): array
    {
        return [];
    }

    protected function defaultCwd(): ?string
    {
        return null;
    }

    protected function defaultEnv(): ?array
    {
        return null;
    }

    protected function defaultInput(): ?string
    {
        return null;
    }

    protected function defaultTimeout(): ?float
    {
        return 60;
    }
}
