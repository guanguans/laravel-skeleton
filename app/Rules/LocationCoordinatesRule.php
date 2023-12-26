<?php

namespace App\Rules;

final class LocationCoordinatesRule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^[-]?((([0-8]?\d)(\.(\d{1,8}))?)|(90(\.0+)?)),\s?[-]?((((1[0-7]\d)|(\d?\d))(\.(\d{1,8}))?)|180(\.0+)?)$/';
    }
}
