<?php

namespace App\Rules;

final class LocationCoordinatesRule extends RegexRule
{
    protected function pattern(): string
    {
        return '/^[-]?((([0-8]?[0-9])(\.(\d{1,8}))?)|(90(\.0+)?)),\s?[-]?((((1[0-7][0-9])|([0-9]?[0-9]))(\.(\d{1,8}))?)|180(\.0+)?)$/';
    }
}
