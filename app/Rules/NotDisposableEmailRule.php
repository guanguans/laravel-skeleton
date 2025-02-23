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

use Illuminate\Support\Str;

/**
 * @see https://github.com/caneara/axiom
 */
final class NotDisposableEmailRule extends Rule
{
    private static array $vendorCache = [];

    private readonly bool $default;

    public function __construct(mixed $default = true)
    {
        $this->default = (bool) $default;
    }

    public static function flushCache(): void
    {
        self::$vendorCache = [];
    }

    public function passes(string $attribute, mixed $value): bool
    {
        try {
            $vendor = Str::after($value, '@');

            return self::$vendorCache[$vendor] ?? self::$vendorCache[$vendor] = $this->isNotDisposable($vendor);
        } catch (\Throwable) {
            return $this->default;
        }
    }

    private function isNotDisposable(string $vendor): bool
    {
        return ! json_decode(file_get_contents("https://open.kickbox.com/v1/disposable/$vendor"), true)['disposable'];
    }
}
