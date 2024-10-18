<?php

/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Support\StreamWrappers;

/**
 * @see https://www.php.net/manual/zh/class.streamwrapper.php
 * @see https://www.php.net/manual/zh/stream.streamwrapper.example-1.php
 * @see https://www.php.net/manual/zh/wrappers.php
 * @see \GuzzleHttp\Psr7\StreamWrapper
 */
abstract class StreamWrapper
{
    /** @var resource */
    public $context;

    abstract public static function name(): string;

    public static function register(): void
    {
        if (! static::isRegistered()) {
            stream_wrapper_register(static::name(), static::class);
        }
    }

    public static function unregister(): void
    {
        if (static::isRegistered()) {
            stream_wrapper_unregister(static::name());
        }
    }

    public static function isRegistered(): bool
    {
        return \in_array(static::name(), stream_get_wrappers(), true);
    }

    public function __construct()
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function dir_closedir(): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function dir_opendir(string $path, int $options): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function dir_readdir(): string
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function dir_rewinddir(): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function mkdir(string $path, int $mode, int $options): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function rename(string $path_from, string $path_to): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function rmdir(string $path, int $options): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * @return resource
     */
    public function stream_cast(int $cast_as)
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function stream_close(): void
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function stream_eof(): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function stream_flush(): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function stream_lock(int $operation): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function stream_metadata(string $path, int $option, mixed $value): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function stream_open(
        string $path,
        string $mode,
        int $options,
        ?string &$opened_path
    ): bool {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function stream_read(int $count): string|false
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function stream_set_option(int $option, int $arg1, int $arg2): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function stream_stat(): array|false
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function stream_tell(): int
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function stream_truncate(int $new_size): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function stream_write(string $data): int
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function unlink(string $path): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function url_stat(string $path, int $flags): array|false
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    public function __destruct()
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * @throws \BadMethodCallException
     */
    private function throwMethodNotImplemented(string $function): void
    {
        throw new \BadMethodCallException(\sprintf('Method [%s::%s] not implemented.', static::class, $function));
    }
}
