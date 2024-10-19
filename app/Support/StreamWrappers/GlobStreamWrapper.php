<?php

/** @noinspection MissingParentCallInspection */

declare(strict_types=1);

namespace App\Support\StreamWrappers;

/**
 * @see https://www.php.net/manual/zh/class.globiterator.php
 */
class GlobStreamWrapper extends StreamWrapper
{
    private array $files = [];

    private int $position = 0;

    /**
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct()
    {
        $this->setContextOptions(array_replace_recursive(
            [self::name() => ['flags' => GLOB_BRACE | GLOB_NOSORT]],
            $this->getContextOptions()
        ));
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

        $files = glob($pattern, $this->getContextOptions()[self::name()]['flags'] ?? 0);
        if ($files === false) {
            return false;
        }

        $this->mergeContextOptions([self::name() => ['pattern' => $pattern]]);
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
        $pattern = $this->getContextOptions()[self::name()]['pattern'];

        sscanf($path, "glob://$pattern/%s", $newPath);

        return stat($newPath);
    }

    private function scanPattern(string $path): ?string
    {
        sscanf($path, 'glob://%s', $pattern);

        return $pattern;
    }

    private function getContextOptions(): array
    {
        return stream_context_get_options($this->getContext());
    }

    private function setContextOptions(array $contextOptions): void
    {
        stream_context_set_options($this->getContext(), $contextOptions);
    }

    private function mergeContextOptions(array $contextOptions): void
    {
        $this->setContextOptions(array_merge_recursive($this->getContextOptions(), $contextOptions));
    }

    private function replaceContextOptions(array $contextOptions): void
    {
        $this->setContextOptions(array_replace_recursive($this->getContextOptions(), $contextOptions));
    }

    /**
     * @return resource|null
     */
    private function getContext()
    {
        return $this->context ?? stream_context_get_default();
    }
}
