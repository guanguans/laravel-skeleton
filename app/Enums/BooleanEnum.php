<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static False()
 * @method static static True()
 */
final class BooleanEnum extends Enum
{
    public const False = false;

    public const True = true;
}
