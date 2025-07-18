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

trait SupportsPathArg
{
    #[\Override]
    public function supports(\SplFileInfo $file): bool
    {
        return str($file)->contains($this->argv());
    }

    /**
     * @noinspection GlobalVariableUsageInspection
     */
    protected function argv(): array
    {
        return $_SERVER['argv'] ?? [];
    }
}
