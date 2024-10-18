<?php

/** @noinspection MissingParentCallInspection */

declare(strict_types=1);

namespace App\Support\StreamWrappers;

class UserFileStreamWrapper extends StreamWrapper
{
    /**
     * @var resource
     */
    private $dirResource;

    /**
     * @var resource
     */
    private $fileResource;

    public static function name(): string
    {
        return 'user-file';
    }

    public function stream_open(string $path, string $mode, int $options, ?string &$opened_path): bool
    {
        sscanf($path, 'user-file://%s', $newPath);
        if ($newPath === null) {
            return false;
        }

        $resource = fopen("file://$newPath", $mode);
        if (! \is_resource($resource)) {
            return false;
        }

        $this->fileResource = $resource;

        return true;
    }

    public function stream_read(int $count): string
    {
        return fread($this->fileResource, $count);
    }

    public function stream_eof(): bool
    {
        return feof($this->fileResource);
    }

    public function stream_write(string $data): int
    {
        return fwrite($this->fileResource, $data);
    }

    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        return fseek($this->fileResource, $offset, $whence) === 0;
    }

    public function stream_tell(): int
    {
        return ftell($this->fileResource);
    }

    public function stream_flush(): bool
    {
        return fflush($this->fileResource);
    }

    public function stream_lock(int $operation): bool
    {
        return flock($this->fileResource, $operation);
    }

    public function stream_truncate(int $new_size): bool
    {
        return ftruncate($this->fileResource, $new_size);
    }

    public function stream_stat(): array|false
    {
        return fstat($this->fileResource);
    }

    public function unlink(string $path): bool
    {
        sscanf($path, 'user-file://%s', $newPath);
        if ($newPath === null) {
            return false;
        }

        return unlink($newPath);
    }

    public function url_stat(string $path, int $flags): array|false
    {
        sscanf($path, 'user-file://%s', $newPath);
        if ($newPath === null) {
            return false;
        }

        return stat($newPath);
    }

    public function dir_opendir(string $path, int $options): bool
    {
        sscanf($path, 'user-file://%s', $newPath);
        if ($newPath === null) {
            return false;
        }

        $resource = opendir($newPath);
        if (! \is_resource($resource)) {
            return false;
        }

        $this->dirResource = $resource;

        return true;
    }

    public function dir_closedir(): bool
    {
        closedir($this->dirResource);

        return true;
    }

    public function dir_readdir(): string
    {
        return readdir($this->dirResource);
    }

    public function dir_rewinddir(): bool
    {
        rewinddir($this->dirResource);

        return true;
    }

    public function mkdir(string $path, int $mode, int $options): bool
    {
        sscanf($path, 'user-file://%s', $newPath);
        if ($newPath === null) {
            return false;
        }

        return mkdir($newPath, $mode);
    }

    public function rename(string $path_from, string $path_to): bool
    {
        sscanf($path_from, 'user-file://%s', $newPathFrom);
        if ($newPathFrom === null) {
            return false;
        }

        sscanf($path_to, 'user-file://%s', $newPathTo);
        if ($newPathTo === null) {
            return false;
        }

        return rename($newPathFrom, $newPathTo);
    }

    public function rmdir(string $path, int $options): bool
    {
        sscanf($path, 'user-file://%s', $newPath);
        if ($newPath === null) {
            return false;
        }

        return rmdir($newPath);
    }
}
