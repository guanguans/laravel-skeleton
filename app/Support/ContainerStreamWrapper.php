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

    private string $path;

    private $id;

    /** @var string r, r+, or w */
    private string $mode;

    /**
     * Returns a resource representing the stream.
     *
     * @return resource
     *
     * @throws \InvalidArgumentException if stream is not readable or writable
     */
    public static function getResource(string $abstract, ?ContainerInterface $container = null)
    {
        self::register();
        $container ??= app();

        $mode = 'w';
        if (
            $container->has($abstract)
            && (\is_string($var = $container->get($abstract)) || $var instanceof \Stringable)
        ) {
            $mode = 'r+';
        }

        return fopen("container://$abstract", $mode, false, self::createStreamContext($container));
    }

    /**
     * Creates a stream context that can be used to open a stream as a php stream resource.
     *
     * @return resource
     */
    public static function createStreamContext(ContainerInterface $container)
    {
        return stream_context_create([
            'container' => ['container' => $container],
        ]);
    }

    /**
     * Registers the stream wrapper if needed
     */
    public static function register(): void
    {
        if (! \in_array('container', stream_get_wrappers(), true)) {
            stream_wrapper_register('container', self::class);
        }
    }

    public function stream_open(string $path, string $mode, int $options, ?string &$opened_path = null): bool
    {
        /** @noinspection SuspiciousAssignmentsInspection */
        $options = stream_context_get_options($this->context);

        if (! isset($options['container']['container'])) {
            return false;
        }

        $this->path = $path;
        $this->id = sscanf($path, 'container://%s')[0];
        $this->mode = $mode;
        $this->container = $options['container']['container'];

        return true;
    }

    public function stream_read(int $count): string
    {
        return $this->container->get($this->id);
    }

    public function stream_write(string $data): int
    {
        $this->container[$this->id] = $data;

        return \strlen($data);
    }

    public function stream_tell(): int
    {
        return \strlen($this->container->get($this->id));
    }

    public function stream_eof(): bool
    {
        return $this->container->has($this->id);
    }

    public function stream_seek(int $offset, int $whence): bool
    {
        return $this->container->has($this->id);
    }

    /**
     * @return false|resource
     */
    public function stream_cast(int $cast_as)
    {
        return self::getResource($this->path, $this->container) ?? false;
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
        if (! $this->container->has($this->id)) {
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
            'size' => \strlen($this->container->get($this->id)) ?: 0,
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
