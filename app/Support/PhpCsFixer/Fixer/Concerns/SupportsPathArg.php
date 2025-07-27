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

namespace App\Support\PhpCsFixer\Fixer\Concerns;

use App\Support\PhpCsFixer\Utils;

trait SupportsPathArg
{
    public function supports(\SplFileInfo $file): bool
    {
        // This is a workaround for the `--path` argument in the command line.
        // It checks if the file path contains the `--path` argument.
        return str($file)->contains(Utils::argv());
    }
}
