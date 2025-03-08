<?php

/** @noinspection MissingParentCallInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\StreamWrappers;

/**
 * @see https://www.php.net/manual/zh/class.globiterator.php
 */
class GlobStreamWrapper extends StreamWrapper
{
    private array $files = [];
    private int $position = 0;

    private function __construct()
    {
        $this->addContextOption('flags', \GLOB_BRACE | \GLOB_NOSORT);
    }

    /**
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function __destruct() {}

    #[\Override]
    final public static function name(): string
    {
        return 'glob';
    }

    #[\Override]
    public static function register(): void
    {
        parent::unregister();
        parent::register();
    }

    #[\Override]
    public function dir_closedir(): bool
    {
        $this->files = [];
        $this->position = 0;

        return true;
    }

    #[\Override]
    public function dir_opendir(string $path, int $options): bool
    {
        $pattern = $this->scanPattern($path);

        if (null === $pattern) {
            return false;
        }

        $files = glob($pattern, $this->getContextOption('flags', 0));

        if (false === $files) {
            return false;
        }

        $this->setContextOption('pattern', $pattern);
        $this->files = $files;
        $this->position = 0;

        return true;
    }

    #[\Override]
    public function dir_readdir(): false|string
    {
        if (!isset($this->files[$this->position])) {
            return false;
        }

        return $this->files[$this->position++];
    }

    #[\Override]
    public function dir_rewinddir(): bool
    {
        $this->position = 0;

        return true;
    }

    #[\Override]
    public function url_stat(string $path, int $flags): array|false
    {
        sscanf($path, "glob://{$this->getContextOption('pattern')}/%s", $newPath);

        return stat($newPath);
    }

    private function scanPattern(string $path): ?string
    {
        sscanf($path, 'glob://%s', $pattern);

        return $pattern;
    }
}
