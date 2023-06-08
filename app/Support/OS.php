<?php

declare(strict_types=1);

namespace App\Support;

class OS
{
    public function isUnix(): bool
    {
        return ! $this->isWindows() && ! $this->isUnknown();
    }

    public function isWindows(): bool
    {
        return 'windows' === $this->family();
    }

    public function isBSD(): bool
    {
        return 'bsd' === $this->family();
    }

    public function isDarwin(): bool
    {
        return 'darwin' === $this->family();
    }

    public function isSolaris(): bool
    {
        return 'solaris' === $this->family();
    }

    public function isLinux(): bool
    {
        return 'linux' === $this->family();
    }

    public function isUnknown(): bool
    {
        return 'unknown' === $this->family();
    }

    public function family(): string
    {
        // @see https://www.php.net/manual/zh/reserved.constants.php
        return strtolower(PHP_OS_FAMILY);
    }
}
