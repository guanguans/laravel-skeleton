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

namespace App\Support\PhpCsFixer\Fixer\Tool;

/**
 * @see https://github.com/dotenv-linter/dotenv-linter
 */
final class DotEnvLinterFixer extends AbstractToolFixer
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function command(): array
    {
        return [
            ...$this->configuration[self::TOOL],
            ...$this->configuration[self::ARGS],
            $this->path(),
        ];
    }

    #[\Override]
    protected function defaultTool(): array
    {
        return ['dotenv-linter'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function defaultArgs(): array
    {
        return ['fix', '--no-color'];
    }

    #[\Override]
    protected function extensions(): array
    {
        return ['env', 'env.example'];
    }
}
