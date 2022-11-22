<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static False()
 * @method static static True()
 */
final class IntegerBooleanEnum extends Enum
{
    public const False = 0;

    public const True = 1;
}
