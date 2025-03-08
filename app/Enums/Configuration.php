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

namespace App\Enums;

use Cerbero\Enum\Concerns\Enumerates;
use Elao\Enum\ExtrasTrait;
use Elao\Enum\ReadableEnumTrait;
use EmreYarligan\EnumConcern\EnumConcern;

/**
 * @see https://masteringlaravel.io/daily/2024-10-14-a-use-case-for-the-value-of-phpdoc-type
 */
enum Configuration: string
{
    // use EnumConcern;
    use Enumerates;
    use ExtrasTrait;
    use ReadableEnumTrait;

    /**
     * @param value-of<self> $value
     */
    public static function tryFromValue(string $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        return null;
    }

    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
}
