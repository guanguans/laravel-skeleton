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

namespace App\Support;

class OS
{
    public function isUnix(): bool
    {
        return !$this->isWindows() && !$this->isUnknown();
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
        return strtolower(\PHP_OS_FAMILY);
    }
}
