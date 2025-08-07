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
 * @see https://github.com/tombi-toml/tombi
 * @see https://github.com/tox-dev/toml-fmt
 */
final class TombiFixer extends AbstractCommandLineToolFixer
{
    #[\Override]
    protected function defaultExtensions(): array
    {
        return ['toml'];
    }

    #[\Override]
    protected function defaultCommand(): array
    {
        return ['tombi', 'format'];
    }

    #[\Override]
    protected function requiredOptions(): array
    {
        return ['--offline', '--no-cache', '--verbose'];
    }
}
