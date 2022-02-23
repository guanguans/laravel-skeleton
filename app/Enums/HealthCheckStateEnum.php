<?php

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
