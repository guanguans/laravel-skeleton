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
