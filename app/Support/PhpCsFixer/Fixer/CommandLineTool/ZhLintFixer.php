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

use App\Support\PhpCsFixer\Utils;

/**
 * @see https://github.com/zhlint-project/zhlint
 */
final class ZhLintFixer extends AbstractCommandLineToolFixer
{
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
        return ['md', 'markdown', 'txt', 'text', '*'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function configurePostNormalisation(): void
    {
        Utils::isDryRun() and $this->createTemporaryFile(
            directory: $this->configuration[self::CWD] ?? getcwd(),
            singleton: false
        );
    }

    protected function singletonPath(): string
    {
        return str(parent::singletonPath())
            ->chopStart($this->configuration[self::CWD] ?? getcwd())
            ->chopStart(\DIRECTORY_SEPARATOR)
            ->toString();
    }
}
