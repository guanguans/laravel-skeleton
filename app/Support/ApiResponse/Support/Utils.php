<?php

declare(strict_types=1);

namespace App\Support\ApiResponse\Support;

class Utils
{
    public static function statusCodeFor(int $code): int
    {
        // return (int) str_pad(substr((string) $code, 0, 3), 3, '0');
        return (int) substr((string) $code, 0, 3);
    }
}
