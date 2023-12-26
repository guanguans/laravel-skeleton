<?php

namespace App\Enums;

class Enum extends \BenSampo\Enum\Enum
{
    /**
     * @noinspection PhpUnused
     * @noinspection UnknownInspectionInspection
     */
    public static function asKeysSelectArray(): array
    {
        return array_combine(self::getKeys(), self::getKeys());
    }

    /**
     * @noinspection PhpUnused
     * @noinspection UnknownInspectionInspection
     */
    public static function asValuesSelectArray(): array
    {
        return array_combine(self::getValues(), self::getValues());
    }
}
