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

use function Illuminate\Filesystem\join_paths;

/**
 * ```php
 * $resource = fopen('user-file://file.txt', 'rb+');
 * $resource = opendir('user-file://dir');
 * ```
 */
class UserFileStreamWrapper extends StreamWrapper
{
    /** @var null|resource */
    private $handle;

    private function __construct()
    {
        $this->replaceGlobalContextOption('file', $this->getContextOptions());
    }

    /**
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function __destruct() {}

    #[\Override]
    final public static function name(): string
    {
        return 'user-file';
    }

    #[\Override]
    public function dir_closedir(): bool
    {
        closedir($this->handle);

        return true;
    }

    #[\Override]
    public function dir_opendir(string $path, int $options): bool
    {
        $newPath = $this->scanPath($path);
        if ($newPath === null) {
            return false;
        }

        $handle = opendir($newPath, $this->getContext());
        if (! \is_resource($handle)) {
            return false;
        }

        $this->handle = $handle;

        return true;
    }

    #[\Override]
    public function dir_readdir(): false|string
    {
        return readdir($this->handle);
    }

    #[\Override]
    public function dir_rewinddir(): bool
    {
        rewinddir($this->handle);

        return true;
    }

    #[\Override]
    public function mkdir(string $path, int $mode, int $options): bool
    {
        $newPath = $this->scanPath($path);
        if ($newPath === null) {
            return false;
        }

        return mkdir($newPath, $mode);
    }

    #[\Override]
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

    #[\Override]
    public function rmdir(string $path, int $options): bool
    {
        $newPath = $this->scanPath($path);
        if ($newPath === null) {
            return false;
        }

        return rmdir($newPath);
    }

    /**
     * @return resource
     */
    #[\Override]
    public function stream_cast(int $castAs): mixed
    {
        switch ($castAs) {
            case STREAM_CAST_AS_STREAM:
            case STREAM_CAST_FOR_SELECT:
                if (! \is_resource($this->handle)) {
                    throw new \RuntimeException("Can't cast resource");
                }

                // @todo cast resource
                // $this->stream_write('casted resource');

                return $this->handle;

            default:
                return throw new \InvalidArgumentException('Invalid cast type');
        }
    }

    #[\Override]
    public function stream_close(): void
    {
        fclose($this->handle);
    }

    #[\Override]
    public function stream_eof(): bool
    {
        return feof($this->handle);
    }

    #[\Override]
    public function stream_flush(): bool
    {
        return fflush($this->handle);
    }

    #[\Override]
    public function stream_lock(int $operation): bool
    {
        return flock($this->handle, $operation);
    }

    /**
     * @noinspection PotentialMalwareInspection
     */
    #[\Override]
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

    #[\Override]
    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool
    {
        if ($useTriggerError = ($options & STREAM_REPORT_ERRORS)) {
            set_error_handler(static function (int $errno, string $errstr): void {
                trigger_error($errstr, $errno);
            });
        }

        $newPath = $this->scanPath($path);
        if ($newPath === null) {
            return false;
        }

        $handle = fopen($newPath, $mode, $useIncludePath = (bool) ($options & STREAM_USE_PATH), $this->getContext());
        if (! \is_resource($handle)) {
            return false;
        }

        if ($useIncludePath) {
            sscanf($newPath, 'file://%s', $purePath);
            foreach (array_map(trim(...), explode(':', get_include_path())) as $includePath) {
                // $fullPath = $includePath.DIRECTORY_SEPARATOR.$purePath;
                $fullPath = join_paths($includePath, $purePath);
                if (file_exists($fullPath)) {
                    $openedPath = $fullPath;

                    break;
                }
            }
        }

        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $openedPath = realpath($newPath) ?: $newPath;
        $this->handle = $handle;

        if ($useTriggerError) {
            restore_error_handler();
        }

        return true;
    }

    #[\Override]
    public function stream_read(int $count): string
    {
        return fread($this->handle, $count);
    }

    #[\Override]
    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        return fseek($this->handle, $offset, $whence) === 0;
    }

    #[\Override]
    public function stream_set_option(int $option, int $arg1, ?int $arg2): bool
    {
        return match ($option) {
            STREAM_OPTION_BLOCKING => stream_set_blocking($this->handle, (bool) $arg1),
            STREAM_OPTION_READ_BUFFER => stream_set_read_buffer($this->handle, $arg2) === 0,
            STREAM_OPTION_WRITE_BUFFER => stream_set_write_buffer($this->handle, $arg2) === 0,
            STREAM_OPTION_READ_TIMEOUT => stream_set_timeout($this->handle, $arg1),
            default => false,
        };
    }

    #[\Override]
    public function stream_stat(): array|false
    {
        return fstat($this->handle);
    }

    #[\Override]
    public function stream_tell(): int
    {
        return ftell($this->handle);
    }

    #[\Override]
    public function stream_truncate(int $newSize): bool
    {
        return ftruncate($this->handle, $newSize);
    }

    #[\Override]
    public function stream_write(string $data): int
    {
        return fwrite($this->handle, $data);
    }

    #[\Override]
    public function unlink(string $path): bool
    {
        $newPath = $this->scanPath($path);
        if ($newPath === null) {
            return false;
        }

        return unlink($newPath);
    }

    #[\Override]
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
