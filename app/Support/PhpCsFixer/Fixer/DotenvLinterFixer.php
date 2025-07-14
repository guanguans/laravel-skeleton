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

/**
 * @see https://github.com/dotenv-linter/dotenv-linter
 */
final class DotenvLinterFixer extends AbstractToolFixer
{
    #[\Override]
    protected function defaultProgram(): array
    {
        return ['dotenv-linter', 'fix'];
    }

    #[\Override]
    protected function supportedExtensions(): array
    {
        return ['env', 'env.example'];
    }
}
