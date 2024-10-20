<?php

/** @noinspection MissingParentCallInspection */

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\StreamWrappers;

/**
 * @see https://www.php.net/manual/zh/class.globiterator.php
 */
class GlobStreamWrapper extends StreamWrapper
{
    private array $files = [];

    private int $position = 0;

    public function __construct()
    {
        $this->addContextOption('flags', GLOB_BRACE | GLOB_NOSORT);
    }

    final public static function name(): string
    {
        return 'glob';
    }

    public static function register(): void
    {
        parent::unregister();
        parent::register();
    }

    public function dir_closedir(): bool
    {
        $this->files = [];
        $this->position = 0;

        return true;
    }

    public function dir_opendir(string $path, int $options): bool
    {
        $pattern = $this->scanPattern($path);
        if ($pattern === null) {
            return false;
        }

        $files = glob($pattern, $this->getContextOption('flags', 0));
        if ($files === false) {
            return false;
        }

        $this->setContextOption('pattern', $pattern);
        $this->files = $files;
        $this->position = 0;

        return true;
    }

    public function dir_readdir(): false|string
    {
        if (! isset($this->files[$this->position])) {
            return false;
        }

        return $this->files[$this->position++];
    }

    public function dir_rewinddir(): bool
    {
        $this->position = 0;

        return true;
    }

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
