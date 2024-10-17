<?php

/** @noinspection MissingReturnTypeInspection */
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support;

use Psr\Container\ContainerInterface;

/**
 * @see https://www.php.net/manual/zh/class.streamwrapper.php
 * @see https://www.php.net/manual/zh/stream.streamwrapper.example-1.php
 * @see \GuzzleHttp\Psr7\StreamWrapper
 */
final class ContainerStreamWrapper
{
    /** @var resource */
    public $context;

    private ContainerInterface $container;

    /** @var string r, r+, or w */
    private string $mode;

    /**
     * Returns a resource representing the stream.
     *
     * @return resource
     *
     * @throws \InvalidArgumentException if stream is not readable or writable
     */
    public static function getResource(ContainerInterface $container)
    {
        self::register();

        return fopen('guzzle://stream', 'rb', false, self::createStreamContext($container));
    }

    /**
     * Creates a stream context that can be used to open a stream as a php stream resource.
     *
     * @return resource
     */
    public static function createStreamContext(ContainerInterface $container)
    {
        return stream_context_create([
            'guzzle' => ['container' => $container],
        ]);
    }

    /**
     * Registers the stream wrapper if needed
     */
    public static function register(): void
    {
        if (! \in_array('guzzle', stream_get_wrappers(), true)) {
            stream_wrapper_register('guzzle', self::class);
        }
    }

    public function stream_open(string $path, string $mode, int $options, ?string &$opened_path = null): bool
    {
        /** @noinspection SuspiciousAssignmentsInspection */
        $options = stream_context_get_options($this->context);

        if (! isset($options['guzzle']['container'])) {
            return false;
        }

        $this->mode = $mode;
        $this->container = $options['guzzle']['container'];

        return true;
    }

    public function stream_read(int $count): string
    {
        return $this->container->read($count);
    }

    public function stream_write(string $data): int
    {
        return $this->container->write($data);
    }

    public function stream_tell(): int
    {
        return $this->container->tell();
    }

    public function stream_eof(): bool
    {
        return $this->container->eof();
    }

    public function stream_seek(int $offset, int $whence): bool
    {
        $this->container->seek($offset, $whence);

        return true;
    }

    /**
     * @return false|resource
     */
    public function stream_cast(int $cast_as)
    {
        $container = clone $this->container;
        $resource = $container->detach();

        /** @noinspection ProperNullCoalescingOperatorUsageInspection */
        return $resource ?? false;
    }

    /**
     * @return array{
     *   dev: int,
     *   ino: int,
     *   mode: int,
     *   nlink: int,
     *   uid: int,
     *   gid: int,
     *   rdev: int,
     *   size: int,
     *   atime: int,
     *   mtime: int,
     *   ctime: int,
     *   blksize: int,
     *   blocks: int
     * }|false
     */
    public function stream_stat(): array|false
    {
        if ($this->container->getSize() === null) {
            return false;
        }

        static $modeMap = [
            'r' => 33060,
            'rb' => 33060,
            'r+' => 33206,
            'w' => 33188,
            'wb' => 33188,
        ];

        return [
            'dev' => 0,
            'ino' => 0,
            'mode' => $modeMap[$this->mode],
            'nlink' => 0,
            'uid' => 0,
            'gid' => 0,
            'rdev' => 0,
            'size' => $this->container->getSize() ?: 0,
            'atime' => 0,
            'mtime' => 0,
            'ctime' => 0,
            'blksize' => 0,
            'blocks' => 0,
        ];
    }

    /**
     * @return array{
     *   dev: int,
     *   ino: int,
     *   mode: int,
     *   nlink: int,
     *   uid: int,
     *   gid: int,
     *   rdev: int,
     *   size: int,
     *   atime: int,
     *   mtime: int,
     *   ctime: int,
     *   blksize: int,
     *   blocks: int
     * }
     */
    public function url_stat(string $path, int $flags): array
    {
        return [
            'dev' => 0,
            'ino' => 0,
            'mode' => 0,
            'nlink' => 0,
            'uid' => 0,
            'gid' => 0,
            'rdev' => 0,
            'size' => 0,
            'atime' => 0,
            'mtime' => 0,
            'ctime' => 0,
            'blksize' => 0,
            'blocks' => 0,
        ];
    }
}
