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

use BenSampo\Enum\Enum;

/**
 * @method static static False()
 * @method static static True()
 */
final class StringBooleanEnum extends Enum
{
    public const string False = '0';
    public const string True = '1';
}
