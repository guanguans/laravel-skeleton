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
 * @see https://github.com/dotenv-linter/dotenv-linter
 */
final class DotenvLinterFixer extends AbstractConfigurableFixer
{
    public const string PROGRAM = 'program';
    public const string ARGUMENTS = 'arguments';
    public const string CWD = 'cwd';
    public const string ENV = 'env';
    public const string INPUT = 'input';
    public const string TIMEOUT = 'timeout';

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
        return new FixerConfigurationResolver([...$this->defaultFixerOptions(), ...$this->fixerOptions()]);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        // $process = $this->createProcess($file, $tokens);
        // dd($process->getCommandLine());
        // $process->run();
        // $process->isSuccessful() and $tokens->setCode(file_get_contents($this->path($file, $tokens)));
    }

    /**
     * @return list<\PhpCsFixer\FixerConfiguration\FixerOptionInterface>
     */
    private function defaultFixerOptions(): array
    {
        return [
            (new FixerOptionBuilder(self::PROGRAM, '.'))
                ->setAllowedTypes(['string', 'array'])
                ->setDefault($this->defaultProgram())
                ->getOption(),
            (new FixerOptionBuilder(self::ARGUMENTS, '.'))
                ->setAllowedTypes(['array'])
                ->setDefault([])
                ->getOption(),
            (new FixerOptionBuilder(self::CWD, '.'))
                ->setAllowedTypes(['string', 'null'])
                ->setDefault(null)
                ->getOption(),
            (new FixerOptionBuilder(self::ENV, '.'))
                ->setAllowedTypes(['array', 'null'])
                ->setDefault(null)
                ->getOption(),
            (new FixerOptionBuilder(self::INPUT, '.'))
                ->setAllowedTypes(['string', 'null'])
                ->setDefault(null)
                ->getOption(),
            (new FixerOptionBuilder(self::TIMEOUT, '.'))
                ->setAllowedTypes(['float', 'int', 'null'])
                ->setDefault(60)
                ->getOption(),
        ];
    }

    /**
     * @return list<\PhpCsFixer\FixerConfiguration\FixerOptionInterface>
     */
    private function fixerOptions(): array
    {
        return [];
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    private function createProcess(\SplFileInfo $file, Tokens $tokens): Process
    {
        return new Process(
            command: [...(array) $this->program(), ...$this->arguments($file, $tokens)],
            cwd: $this->configuration[self::CWD],
            env: $this->configuration[self::ENV],
            input: $this->configuration[self::INPUT],
            timeout: $this->configuration[self::TIMEOUT],
        );
    }

    private function program(): array|string
    {
        return $this->defaultProgram();
    }

    private function defaultProgram(): array|string
    {
        return ['dotenv-linter', 'fix'];
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    private function arguments(\SplFileInfo $file, Tokens $tokens): array
    {
        return [$this->path($file, $tokens), ...$this->configuration[self::ARGUMENTS]];
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens<\PhpCsFixer\Tokenizer\Token> $tokens
     */
    private function path(\SplFileInfo $file, Tokens $tokens): string
    {
        $path = (string) $file;

        if (self::isDryRun()) {
            file_put_contents($path = self::createTemporaryFile(), $tokens->generateCode());
        }

        return $path;
    }

    private static function isDryRun(): bool
    {
        return \in_array('--dry-run', self::argv(), true);
    }

    /**
     * @noinspection GlobalVariableUsageInspection
     */
    private static function argv(): array
    {
        return $_SERVER['argv'] ?? [];
    }

    private static function createTemporaryFile(): string
    {
        static $temporaryFile;

        return $temporaryFile ??= create_temporary_file();
    }
}
