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

final class LenientPortRule extends AggregateRule
{
    protected function rules(): array
    {
        return [
            'int',
            'between:1,65535',
        ];
    }
}
