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

namespace App\Rules;

final class HexRule extends RegexRule
{
    public function __construct(private readonly bool $forceFull = false, private readonly bool $allowAlpha = false) {}

    #[\Override]
    protected function pattern(): string
    {
        $pattern = '/^#([a-fA-F0-9]{6}';

        if (!$this->forceFull) {
            $pattern .= '|[a-fA-F0-9]{3}';
        }

        if ($this->allowAlpha) {
            $pattern .= '|[a-fA-F0-9]{8}';

            if (!$this->forceFull) {
                $pattern .= '|[a-fA-F0-9]{4}';
            }
        }

        return $pattern.')$/';
    }
}
