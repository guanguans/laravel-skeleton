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

abstract class RegexRule extends Rule
{
    public function passes(string $attribute, mixed $value): bool
    {
        return (bool) preg_match($this->pattern(), $value);
    }

    /**
     * REGEX pattern of rule
     */
    abstract protected function pattern(): string;
}
