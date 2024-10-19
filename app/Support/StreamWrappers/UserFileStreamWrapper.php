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
 * ```php
 * $resource = fopen('user-file://file.txt', 'rb+');
 * $resource = opendir('user-file://dir');
 * ```
 */
class UserFileStreamWrapper extends StreamWrapper
{
    /** @var null|resource */
    private $resource;

    final public static function name(): string
    {
        return 'user-file';
    }

    public function dir_closedir(): bool
    {
        closedir($this->resource);

        return true;
    }

    public function dir_opendir(string $path, int $options): bool
    {
        $newPath = $this->scanPath($path);
        if ($newPath === null) {
            return false;
        }

        $resource = opendir($newPath);
        if (! \is_resource($resource)) {
            return false;
        }

        $this->resource = $resource;

        return true;
    }

    public function dir_readdir(): false|string
    {
        return readdir($this->resource);
    }

    public function dir_rewinddir(): bool
    {
        rewinddir($this->resource);

        return true;
    }

    public function mkdir(string $path, int $mode, int $options): bool
    {
        $newPath = $this->scanPath($path);
        if ($newPath === null) {
            return false;
        }

        return mkdir($newPath, $mode);
    }

    public function rename(string $pathFrom, string $pathTo): bool
    {
        $newPathFrom = $this->scanPath($pathFrom);
        if ($newPathFrom === null) {
            return false;
        }

        $newPathTo = $this->scanPath($pathTo);
        if ($newPathTo === null) {
            return false;
        }

        return rename($newPathFrom, $newPathTo);
    }

    public function rmdir(string $path, int $options): bool
    {
        $newPath = $this->scanPath($path);
        if ($newPath === null) {
            return false;
        }

        return rmdir($newPath);
    }

    /**
     * {@inheritdoc}
     *
     * @noinspection MissingReturnTypeInspection
     */
    public function stream_cast(int $castAs)
    {
        switch ($castAs) {
            case STREAM_CAST_AS_STREAM:
            case STREAM_CAST_FOR_SELECT:
                if (! \is_resource($this->resource)) {
                    throw new \RuntimeException("Can't cast resource");
                }

                // @todo cast resource
                // $this->stream_write('casted resource');

                return $this->resource;

            default:
                return throw new \InvalidArgumentException('Invalid cast type');
        }
    }

    public function stream_close(): void
    {
        fclose($this->resource);
    }

    public function stream_eof(): bool
    {
        return feof($this->resource);
    }

    public function stream_flush(): bool
    {
        return fflush($this->resource);
    }

    public function stream_lock(int $operation): bool
    {
        return flock($this->resource, $operation);
    }

    /**
     * @noinspection PotentialMalwareInspection
     */
    public function stream_metadata(string $path, int $option, mixed $value): bool
    {
        $newPath = $this->scanPath($path);
        if ($newPath === null) {
            return false;
        }

        return match ($option) {
            STREAM_META_TOUCH => touch($newPath, $mtime = $value[0] ?? time(), $value[1] ?? $mtime),
            STREAM_META_OWNER_NAME, STREAM_META_OWNER => chown($newPath, $value),
            STREAM_META_GROUP_NAME, STREAM_META_GROUP => chgrp($newPath, $value),
            STREAM_META_ACCESS => chmod($newPath, $value),
            default => false,
        };
    }

    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool
    {
        $newPath = $this->scanPath($path);
        if ($newPath === null) {
            return false;
        }

        $resource = fopen($newPath, $mode);
        if (! \is_resource($resource)) {
            return false;
        }

        $this->resource = $resource;

        return true;
    }

    public function stream_read(int $count): string
    {
        return fread($this->resource, $count);
    }

    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        return fseek($this->resource, $offset, $whence) === 0;
    }

    public function stream_set_option(int $option, int $arg1, ?int $arg2): bool
    {
        return match ($option) {
            STREAM_OPTION_BLOCKING => stream_set_blocking($this->resource, (bool) $arg1),
            STREAM_OPTION_READ_BUFFER => stream_set_read_buffer($this->resource, $arg2) === 0,
            STREAM_OPTION_WRITE_BUFFER => stream_set_write_buffer($this->resource, $arg2) === 0,
            STREAM_OPTION_READ_TIMEOUT => stream_set_timeout($this->resource, $arg1),
            default => false,
        };
    }

    public function stream_stat(): array|false
    {
        return fstat($this->resource);
    }

    public function stream_tell(): int
    {
        return ftell($this->resource);
    }

    public function stream_truncate(int $newSize): bool
    {
        return ftruncate($this->resource, $newSize);
    }

    public function stream_write(string $data): int
    {
        return fwrite($this->resource, $data);
    }

    public function unlink(string $path): bool
    {
        $newPath = $this->scanPath($path);
        if ($newPath === null) {
            return false;
        }

        return unlink($newPath);
    }

    public function url_stat(string $path, int $flags): array|false
    {
        $newPath = $this->scanPath($path);
        if ($newPath === null) {
            return false;
        }

        return stat($newPath);
    }

    private function scanPath(string $path): ?string
    {
        sscanf($path, 'user-file://%s', $newPath);
        if ($newPath === null) {
            return null;
        }

        sscanf($path, 'user-%s', $newPath);

        return $newPath;
    }
}
