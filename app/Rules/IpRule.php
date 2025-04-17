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

use Symfony\Component\HttpFoundation\IpUtils;

final class IpRule extends Rule
{
    /** @var list<string> */
    private static array $v4 = [
        '0.0.0.0/8',
        '10.0.0.0/8',
        '127.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16',
        '169.254.0.0/16',
    ];

    /** @var list<string> */
    private static array $v6 = [
        '::1/128',
        'fc00::/7',
        'fd00::/8',
        'fe80::/10',
    ];

    public function __construct(private readonly bool $isPublic = true) {}

    public function isV4(string $ip): bool
    {
        return !$this->isV6($ip);
    }

    public function isV6(string $ip): bool
    {
        return substr_count($ip, ':') > 1;
    }

    public function isPrivate(string $ip): bool
    {
        $ips = $this->isV4($ip) ? self::$v4 : self::$v6;

        return IpUtils::checkIp($ip, $ips);
    }

    public function isPublic(string $ip): bool
    {
        return !$this->isPrivate($ip);
    }

    #[\Override]
    public function passes(string $attribute, mixed $value): bool
    {
        return $this->isPublic ? $this->isPublic($value) : $this->isPrivate($value);
    }
}
