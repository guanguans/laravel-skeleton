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

trait SupportsExtensions
{
    public function supports(\SplFileInfo $file): bool
    {
        return str($file->getExtension())->is($this->supportsExtensions(), true)
            || str($file->getPathname())->lower()->endsWith($this->supportsExtensions());
    }

    /**
     * @return list<string>
     */
    abstract protected function supportsExtensions(): array;
}
