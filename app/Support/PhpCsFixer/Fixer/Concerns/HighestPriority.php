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

trait HighestPriority
{
    #[\Override]
    public function getPriority(): int
    {
        return $this->getHighestPriority();
    }

    /**
     * @see \PhpCsFixer\Fixer\Basic\NonPrintableCharacterFixer::getPriority()
     * @see \PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer::getPriority()
     */
    protected function getHighestPriority(): int
    {
        return \PHP_INT_MAX;
    }
}
