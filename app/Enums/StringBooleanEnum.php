<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static False()
 * @method static static True()
 */
final class StringBooleanEnum extends Enum
{
    public const False = '0';

    public const True = '1';
}
