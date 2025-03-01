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

use Illuminate\Support\Facades\Hash;

final class CurrentUserPasswordRule extends Rule
{
    #[\Override]
    public function passes(string $attribute, mixed $value): bool
    {
        return Hash::check($value, auth()->user()?->getAuthPassword());
    }
}
