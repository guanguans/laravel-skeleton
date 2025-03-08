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
 * @method static static OK()
 * @method static static WARNING()
 * @method static static FAILING()
 */
final class HealthCheckStateEnum extends Enum
{
    public const string OK = '<info>ok</info>';
    public const string WARNING = '<comment>warning</comment>';
    public const string FAILING = '<error>failing</error>';
}
