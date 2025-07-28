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

use App\Support\PhpCsFixer\Fixer\CommandLineTool\Concerns\Cmd;

/**
 * @see https://github.com/zhlint-project/zhlint
 */
final class ZhLintFixer extends AbstractCommandLineToolFixer
{
    use Cmd;

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function supports(\SplFileInfo $file): bool
    {
        return (bool) preg_match('/(zh|cn|chinese).*\.(md|markdown|text|txt)$/mi', $file->getBasename());
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

    #[\Override]
    protected function extensions(): array
    {
        return ['md', 'markdown', 'txt', 'text'];
    }

    protected function path(): string
    {
        return str(parent::path())
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
}
