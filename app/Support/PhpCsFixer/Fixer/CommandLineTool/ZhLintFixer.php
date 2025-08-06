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

namespace App\Support\PhpCsFixer\Fixer\CommandLineTool;

use PhpCsFixer\Tokenizer\Tokens;

/**
 * @see https://github.com/zhlint-project/zhlint
 */
final class ZhLintFixer extends AbstractCommandLineToolFixer
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function supports(\SplFileInfo $file): bool
    {
        return (bool) preg_match('/(zh|cn|chinese).*\.(md|markdown|text|txt)$/mi', $file->getBasename());
    }

    #[\Override]
    protected function defaultExtensions(): array
    {
        return ['zh_CN.md'];
    }

    #[\Override]
    protected function defaultCommand(): array
    {
        return ['zhlint'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function requiredOptions(): array
    {
        return ['--fix'];
    }

    /**
     * @noinspection SensitiveParameterInspection
     */
    protected function finalFile(\SplFileInfo $file, Tokens $tokens): string
    {
        return str(parent::finalFile($file, $tokens))
            ->chopStart($this->cmd())
            ->chopStart(\DIRECTORY_SEPARATOR)
            ->toString();
    }

    protected function createTemporaryFile(
        ?string $directory = null,
        ?string $prefix = null,
        ?string $extension = null,
        bool $deferDelete = true,
    ): string {
        return parent::createTemporaryFile($this->cmd(), $prefix, $extension, $deferDelete);
    }

    private function cmd(): string
    {
        return $this->configuration[self::CWD] ?? getcwd();
    }
}
