<?php

namespace App\Support;

/**
 * @see https://www.php.net/manual/zh/class.streamwrapper.php
 * @see https://www.php.net/manual/zh/stream.streamwrapper.example-1.php
 * @see https://www.php.net/manual/zh/wrappers.php
 * @see \GuzzleHttp\Psr7\StreamWrapper
 */
class UserGlobStreamWrapper
{
    public const NAME = 'user-glob';

    /** @var resource */
    public $context;

    private string $path;

    private string $mode;

    private int $options;

    private ?string $openedPath;

    private array $files;

    private int $position;

    /**
     * @param  resource|null  $context
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
            self::NAME => [
                'flags' => $flags,
            ],
        ]);
    }

    public static function register()
    {
        if (! \in_array(self::NAME, stream_get_wrappers())) {
            stream_wrapper_register(self::NAME, self::class);
        }
    }

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $contextOptions = stream_context_get_options($this->context);

        $flags = $contextOptions[self::NAME]['flags'] ?? 0;

        if (! sscanf($path, 'user-glob://%s', $pattern) || ($files = glob($pattern, $flags)) === false) {
            return false;
        }

        $this->path = $path;
        $this->mode = $mode;
        $this->options = $options;
        $this->openedPath = $opened_path;
        $this->files = $files;
        $this->position = 0;

        return true;
    }

    public function stream_read($count)
    {
        if (isset($this->files[$count])) {
            $file = $this->files[$count];
            $this->position = $count + 1;

            return $file;
        }

        return false;
    }

    public function stream_eof()
    {
        return $this->position >= \count($this->files);
    }

    public function stream_stat()
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
}
