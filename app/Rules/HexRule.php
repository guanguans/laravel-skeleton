<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Rules;

final class HexRule extends RegexRule
{
    public function __construct(private readonly bool $forceFull = false, private readonly bool $allowAlpha = false) {}

    protected function pattern(): string
    {
        $pattern = '/^#([a-fA-F0-9]{6}';

        if (! $this->forceFull) {
            $pattern .= '|[a-fA-F0-9]{3}';
        }

        if ($this->allowAlpha) {
            $pattern .= '|[a-fA-F0-9]{8}';

            if (! $this->forceFull) {
                $pattern .= '|[a-fA-F0-9]{4}';
            }
        }

        return $pattern.')$/';
    }
}
