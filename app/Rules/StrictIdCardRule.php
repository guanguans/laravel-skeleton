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

use App\Support\IdCard;

final class StrictIdCardRule extends Rule
{
    public function passes(string $attribute, mixed $value): bool
    {
        return IdCard::passes($value);
    }
}
