<?php

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
 * @see https://www.php.net/manual/zh/class.streamwrapper.php
 * @see https://www.php.net/manual/zh/stream.streamwrapper.example-1.php
 * @see https://www.php.net/manual/zh/wrappers.php
 * @see \GuzzleHttp\Psr7\StreamWrapper
 */
class GlobStreamWrapper extends StreamWrapper
{
    /** @var resource */
    public $context;

    private string $path;

    private string $mode;

    private int $options;

    private ?string $openedPath;

    private array $files;

    private int $position;

    final public static function name(): string
    {
        return 'glob';
    }

    /**
     * @param  null|resource  $context
     * @return false|resource
     */
    public static function resourceFor(
        string $pattern,
        string $mode = 'rb',
        bool $useIncludePath = false,
        $context = null,
        int $flags = 0,
    ) {
        self::register();

        return fopen($pattern, $mode, $useIncludePath, $context ?? self::createStreamContext($flags));
    }

    /**
     * @return resource
     */
    public static function createStreamContext(int $flags = 0)
    {
        return stream_context_create([
            self::name() => [
                'flags' => $flags,
            ],
        ]);
    }

    public function stream_open($path, $mode, $options, &$opened_path): bool
    {
        $contextOptions = stream_context_get_options($this->context);

        $flags = $contextOptions[self::name()]['flags'] ?? 0;

        if (! sscanf($path, 'glob://%s', $pattern) || ($files = glob($pattern, $flags)) === false) {
            return false;
        }

        $this->path = $path;
        $this->mode = $mode;
        $this->options = $options;
        $this->openedPath = $opened_path = $pattern;
        $this->files = $files;
        $this->position = 0;

        return true;
    }

    public function stream_read($count): false|string
    {
        if (isset($this->files[$count])) {
            $file = $this->files[$count];
            $this->position = $count + 1;

            return $file;
        }

        return false;
    }

    public function stream_eof(): bool
    {
        return $this->position >= \count($this->files);
    }

    public function stream_stat(): false|array
    {
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
            'size' => \count($this->files),
            'atime' => 0,
            'mtime' => 0,
            'ctime' => 0,
            'blksize' => 0,
            'blocks' => 0,
        ];
    }

    public function dir_opendir(string $path, int $options): bool
    {
        \is_resource($this->context)
            ? $contextOptions = stream_context_get_options($this->context)
            : $contextOptions = [];

        $flags = $contextOptions[self::name()]['flags'] ?? 0;

        if (! sscanf($path, 'glob://%s', $pattern) || ($files = glob($pattern, $flags)) === false) {
            return false;
        }

        array_unshift($files, $files[0]);

        $this->path = $path;
        // $this->mode = $mode;
        $this->options = $options;
        // $this->openedPath = $opened_path = $pattern;
        $this->files = $files;
        $this->position = 0;

        return true;
    }

    public function dir_readdir(): string
    {
        if (isset($this->files[$this->position])) {
            $file = $this->files[$this->position];
            ++$this->position;

            return $file;
        }

        return false;
    }

    public function url_stat(string $path, int $flags): array
    {
        sscanf($path, 'glob://*.php/%s', $file);

        return stat($file);
    }
}
