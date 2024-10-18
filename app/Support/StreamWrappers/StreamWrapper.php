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

    /**
     * Constructs a new stream wrapper
     */
    public function __construct() {}

    /**
     * Destructs an existing stream wrapper
     */
    public function __destruct() {}

    /**
     * Close directory handle
     *
     * @see \closedir()
     */
    public function dir_closedir(): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Open directory handle
     *
     * @see \opendir()
     */
    public function dir_opendir(string $path, int $options): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Read entry from directory handle
     *
     * @see \readdir()
     */
    public function dir_readdir(): string
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Rewind directory handle
     *
     * @see \rewinddir()
     */
    public function dir_rewinddir(): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Create a directory
     */
    public function mkdir(string $path, int $mode, int $options): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Renames a file or directory
     */
    public function rename(string $path_from, string $path_to): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Removes a directory
     */
    public function rmdir(string $path, int $options): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Retrieve the underlaying resource
     *
     * @return resource
     */
    public function stream_cast(int $cast_as): mixed
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Close a resource
     *
     * @see \fclose()
     */
    public function stream_close(): void {}

    /**
     * Tests for end-of-file on a file pointer
     *
     * @see \feof()
     */
    public function stream_eof(): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Flushes the output
     *
     * @see \fflush()
     */
    public function stream_flush(): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Advisory file locking
     *
     * @see \flock()
     */
    public function stream_lock(int $operation): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Change stream metadata
     */
    public function stream_metadata(string $path, int $option, mixed $value): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Opens file or URL
     *
     * @see \fopen()
     */
    public function stream_open(string $path, string $mode, int $options, ?string &$opened_path): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Read from stream
     *
     * @see \fread()
     */
    public function stream_read(int $count): string|false
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Seeks to specific location in a stream
     *
     * @see \fseek()
     */
    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Change stream options
     */
    public function stream_set_option(int $option, int $arg1, int $arg2): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Retrieve information about a file resource
     *
     * @see \fstat()
     */
    public function stream_stat(): array|false
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Retrieve the current position of a stream
     *
     * @see \ftell()
     */
    public function stream_tell(): int
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Truncate stream
     *
     * @see \ftruncate()
     */
    public function stream_truncate(int $new_size): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Write to stream
     *
     * @see \fwrite()
     */
    public function stream_write(string $data): int
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Delete a file
     */
    public function unlink(string $path): bool
    {
        $this->throwMethodNotImplemented(__FUNCTION__);
    }

    /**
     * Retrieve information about a file
     */
    public function url_stat(string $path, int $flags): array|false
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
