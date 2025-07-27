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

/**
 * @see https://github.com/reteps/dockerfmt
 * @see https://github.com/hadolint/hadolint
 */
final class DockerFmtFixer extends AbstractCommandLineToolFixer
{
    #[\Override]
    protected function defaultCommand(): array
    {
        return ['dockerfmt'];
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function requiredOptions(): array
    {
        return ['--write', '--newline', '--space-redirects'];
    }

    #[\Override]
    protected function extensions(): array
    {
        return ['dockerfile'];
    }
}
