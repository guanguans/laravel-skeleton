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
 * @method static static OK()
 * @method static static WARNING()
 * @method static static FAILING()
 */
final class HealthCheckStateEnum extends Enum
{
    public const OK = '<info>ok</info>';

    public const WARNING = '<comment>warning</comment>';

    public const FAILING = '<error>failing</error>';
}
