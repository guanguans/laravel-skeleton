<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Rules;

final class LocationCoordinatesRule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^[-]?((([0-8]?\d)(\.(\d{1,8}))?)|(90(\.0+)?)),\s?[-]?((((1[0-7]\d)|(\d?\d))(\.(\d{1,8}))?)|180(\.0+)?)$/';
    }
}
