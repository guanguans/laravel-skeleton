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
