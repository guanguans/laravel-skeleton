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

final class MacAddressRule extends Rule
{
    #[\Override]
    public function passes(string $attribute, mixed $value): bool
    {
        $value = preg_replace('/[\. :-]/i', '', $value);

        return (bool) preg_match('/^[0-9a-f]{12}$/i', $value);
    }
}
