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

trait SupportsExtensionsAndPathArg
{
    use SupportsExtensions {
        SupportsExtensions::supports as supportsExtensions;
    }
    use SupportsPathArg{
        SupportsPathArg::supports as supportsPathArg;
    }

    #[\Override]
    public function supports(\SplFileInfo $file): bool
    {
        return $this->supportsExtensions($file) && $this->supportsPathArg($file);
    }
}
