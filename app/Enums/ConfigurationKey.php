<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Enums;

/**
 * @see https://github.com/cerbero90/enum
 * @see https://github.com/Elao/PhpEnums
 * @see https://github.com/emreyarligan/enum-concern
 * @see https://masteringlaravel.io/daily/2024-10-14-a-use-case-for-the-value-of-phpdoc-type
 * @see https://github.com/anisAronno/laravel-starter/blob/develop/app/Helpers/CacheKey.php
 */
enum ConfigurationKey: string
{
    // use EnumConcern;
    // use Enumerates;
    // use ExtrasTrait;
    // use ReadableEnumTrait;
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
}
