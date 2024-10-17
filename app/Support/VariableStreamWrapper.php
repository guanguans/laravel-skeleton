<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support;

/**
 * @see https://www.php.net/manual/zh/class.streamwrapper.php
 * @see https://www.php.net/manual/zh/stream.streamwrapper.example-1.php
 * @see \GuzzleHttp\Psr7\StreamWrapper
 */
class VariableStreamWrapper
{
    public $position;

    public $varname;

    /** @var resource */
    public $context;

    public function stream_open($path, $mode, $options, &$openedPath): bool
    {
        $url = parse_url($path);
        $this->varname = $url['host'];
        $this->position = 0;

        return true;
    }

    public function stream_read($count): string
    {
        $ret = substr($GLOBALS[$this->varname], $this->position, $count);
        $this->position += \strlen($ret);

        return $ret;
    }

    public function stream_write($data): int
    {
        $left = substr($GLOBALS[$this->varname], 0, $this->position);
        $right = substr($GLOBALS[$this->varname], $this->position + \strlen($data));
        $GLOBALS[$this->varname] = $left.$data.$right;
        $this->position += \strlen($data);

        return \strlen($data);
    }

    public function stream_tell()
    {
        return $this->position;
    }

    public function stream_eof(): bool
    {
        return $this->position >= \strlen($GLOBALS[$this->varname]);
    }

    public function stream_seek($offset, $whence): bool
    {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < \strlen($GLOBALS[$this->varname]) && $offset >= 0) {
                    $this->position = $offset;

                    return true;
                }

                return false;

            case SEEK_CUR:
                if ($offset >= 0) {
                    $this->position += $offset;

                    return true;
                }

                return false;

            case SEEK_END:
                if (\strlen($GLOBALS[$this->varname]) + $offset >= 0) {
                    $this->position = \strlen($GLOBALS[$this->varname]) + $offset;

                    return true;
                }

                return false;

            default:
                return false;
        }
    }

    public function stream_metadata($path, $option, $var): bool
    {
        if ($option === STREAM_META_TOUCH) {
            $url = parse_url($path);
            $varname = $url['host'];
            if (! isset($GLOBALS[$varname])) {
                $GLOBALS[$varname] = '';
            }

            return true;
        }

        return false;
    }
}
