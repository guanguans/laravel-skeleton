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

namespace App\Support\PhpCsFixer\Fixer\Tool;

use App\Support\PhpCsFixer\Fixer\AbstractConfigurableFixer;
use App\Support\PhpCsFixer\Fixer\Concerns\AllowRisky;
use App\Support\PhpCsFixer\Fixer\Concerns\Awarer;
use App\Support\PhpCsFixer\Fixer\Concerns\HighestPriority;
use App\Support\PhpCsFixer\Fixer\Concerns\InlineHtmlCandidate;
use App\Support\PhpCsFixer\Fixer\Concerns\IsDryRun;
use App\Support\PhpCsFixer\Fixer\Concerns\LowestPriority;
use App\Support\PhpCsFixer\Fixer\Concerns\SupportsExtensions;
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
    use AllowRisky;
    use Awarer;
    use InlineHtmlCandidate;
    use IsDryRun;

    // use HighestPriority;
    use LowestPriority;
    use SupportsExtensions;
    public const string TOOL = 'tool';
    public const string ARGS = 'args';
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
            (new FixerOptionBuilder(self::TOOL, 'The tool to run, e.g. `blade-formatter`.'))
                ->setAllowedTypes(['array'])
                ->setDefault($this->defaultTool())
                ->getOption(),
            (new FixerOptionBuilder(self::ARGS, 'The args to pass to the tool.'))
                ->setAllowedTypes(['array'])
                ->setDefault($this->defaultArgs())
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
        $this->setFileAndTokens($file, $tokens);

        $process = $this->createProcess($file, $tokens);
        // dd($process->getCommandLine());
        $process->run();

        if ($this->isProcessSuccessful($process)) {
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

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    protected function command(\SplFileInfo $file, Tokens $tokens): array
    {
        return [
            ...(array) $this->configuration[self::TOOL],
            $this->path($file, $tokens),
            ...$this->configuration[self::ARGS],
        ];
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

        if ($this->isDryRun()) {
            file_put_contents($path = $this->createTemporaryFile(), $tokens->generateCode());
        }

        return $path;
    }

    protected function createTemporaryFile(): string
    {
        return self::$temporaryFile ??= create_temporary_file();
    }

    protected function isProcessSuccessful(Process $process): bool
    {
        return $process->isSuccessful();
    }

    abstract protected function defaultTool(): array;

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
