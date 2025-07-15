<?php

/** @noinspection PhpConstantNamingConventionInspection */
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

use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Process\Process;
use function Psl\Filesystem\create_temporary_file;

/**
 * @see https://github.com/super-linter/super-linter
 */
abstract class AbstractToolFixer extends AbstractConfigurableFixer
{
    public const string PROGRAM = 'program';
    public const string ARGUMENTS = 'arguments';
    public const string CWD = 'cwd';
    public const string ENV = 'env';
    public const string INPUT = 'input';
    public const string TIMEOUT = 'timeout';
    protected static ?string $temporaryFile = null;

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
            $summary = \sprintf('Format a file by %s.', str($this->getSortName())->headline()->lower()),
            [new CodeSample($summary)]
        );
    }

    #[\Override]
    public function supports(\SplFileInfo $file): bool
    {
        return str($file->getExtension())->is($this->supportsExtensions(), true)
            || str($file->getPathname())->lower()->endsWith($this->supportsExtensions());
    }

    #[\Override]
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver($this->fixerOptions());
    }

    /**
     * @return list<\PhpCsFixer\FixerConfiguration\FixerOptionInterface>
     */
    protected function fixerOptions(): array
    {
        return [
            (new FixerOptionBuilder(self::PROGRAM, '.'))
                ->setAllowedTypes(['string', 'array'])
                ->setDefault($this->defaultProgram())
                ->getOption(),
            (new FixerOptionBuilder(self::ARGUMENTS, '.'))
                ->setAllowedTypes(['array'])
                ->setDefault($this->defaultArguments())
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
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    #[\Override]
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $process = $this->createProcess($file, $tokens);
        // dd($process->getCommandLine());
        $process->run();

        if ($this->isProcessSuccessful($process, $file, $tokens)) {
            $tokens->setCode(file_get_contents($this->path($file, $tokens)));
        }
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    protected function createProcess(\SplFileInfo $file, Tokens $tokens): Process
    {
        return new Process(
            command: $this->command($file, $tokens),
            cwd: $this->configuration[self::CWD],
            env: $this->configuration[self::ENV],
            input: $this->configuration[self::INPUT],
            timeout: $this->configuration[self::TIMEOUT],
        );
    }

    protected function command(\SplFileInfo $file, Tokens $tokens): array
    {
        return [...(array) $this->program(), ...$this->arguments($file, $tokens)];
    }

    protected function program(): array|string
    {
        return $this->defaultProgram();
    }

    /**
     * @return iterable<string>|string
     */
    abstract protected function supportsExtensions(): array|string;

    abstract protected function defaultProgram(): array|string;

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    protected function arguments(\SplFileInfo $file, Tokens $tokens): array
    {
        return [$this->path($file, $tokens), ...$this->configuration[self::ARGUMENTS]];
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    protected function path(\SplFileInfo $file, Tokens $tokens): string
    {
        static $path;

        if ($path) {
            return $path;
        }

        $path = (string) $file;

        if (self::isDryRun()) {
            file_put_contents($path = self::createTemporaryFile(), $tokens->generateCode());
        }

        return $path;
    }

    protected static function isDryRun(): bool
    {
        return \in_array('--dry-run', self::argv(), true);
    }

    /**
     * @noinspection GlobalVariableUsageInspection
     */
    protected static function argv(): array
    {
        return $_SERVER['argv'] ?? [];
    }

    protected static function createTemporaryFile(): string
    {
        return self::$temporaryFile ??= create_temporary_file();
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    protected function isProcessSuccessful(Process $process, \SplFileInfo $file, Tokens $tokens): bool
    {
        return $process->isSuccessful();
    }

    protected function defaultArguments(): array
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

    protected function defaultTimeout(): int
    {
        return 60;
    }
}
