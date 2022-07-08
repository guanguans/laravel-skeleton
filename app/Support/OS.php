<?php

namespace App\Support;

class OS
{
    public function isUnix(): bool
    {
        return ! $this->isWindows() && ! $this->isUnknown();
    }

    public function isWindows(): bool
    {
        return $this->family() === 'windows';
    }

    public function isBSD(): bool
    {
        return $this->family() === 'bsd';
    }

    public function isDarwin(): bool
    {
        return $this->family() === 'darwin';
    }

    public function isSolaris(): bool
    {
        return $this->family() === 'solaris';
    }

    public function isLinux(): bool
    {
        return $this->family() === 'linux';
    }

    public function isUnknown(): bool
    {
        return $this->family() === 'unknown';
    }

    public function family(): string
    {
        /** @see https://www.php.net/manual/zh/reserved.constants.php */
        return strtolower(PHP_OS_FAMILY);
    }
}
