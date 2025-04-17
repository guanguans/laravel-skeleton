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

namespace App\Rules;

final class LocationCoordinatesRule extends RegexRule
{
    #[\Override]
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^-?((([0-8]?\d)(\.(\d{1,8}))?)|(90(\.0+)?)),\s?-?((((1[0-7]\d)|(\d?\d))(\.(\d{1,8}))?)|180(\.0+)?)$/';
    }
}
