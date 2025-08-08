<?php

/** @noinspection PhpInternalEntityUsedInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Signers;

final class Utils
{
    private function __construct() {}

    public static function simpleHttpBuildQuery(array|object $data): string
    {
        if (!$data instanceof \Traversable) {
            $data = (array) $data;
        }

        reset($data);
        $query = '';

        foreach ($data as $key => $value) {
            if (null === $value) {
                continue;
            }

            $value = match ($value) {
                0,false => '0',
                default => $value
            };

            $query .= "&$key=$value";
        }

        return substr($query, 1);
    }

    public static function defaultHttpBuildQuery(array|object $data): string
    {
        $toQuery = static function (?string $mainKey, mixed $data) use (&$toQuery): string {
            if (!$data instanceof \Traversable) {
                $data = (array) $data;
            }

            reset($data);
            $query = '';

            foreach ($data as $key => $value) {
                if (null === $value) {
                    continue;
                }

                $value = match ($value) {
                    0,false => '0',
                    default => $value
                };

                if (null !== $mainKey) {
                    $key = "{$mainKey}[$key]";
                }

                $query .= \is_scalar($value) ? "&$key=$value" : $toQuery($key, $value); // 递归调用
            }

            return $query;
        };

        return substr($toQuery(null, $data), 1);
    }

    /**
     * http_build_query 的实现。
     *
     * @see \http_build_query()
     * @see https://laravel-news.com/retrieve-the-currently-executing-closure-in-php-85
     *
     * @noinspection D
     *
     * ```
     * $queryPayload = [
     *     1 => 'a',
     *     '10' => 'b',
     *     '01' => 'c',
     *     'keyO1' => null,
     *     'keyO2' => false,
     *     'keyO3' => true,
     *     'keyO4' => 0,
     *     'keyO5' => 1,
     *     'keyO6' => 0.0,
     *     'keyO7' => 0.1,
     *     'keyO8' => [],
     *     'keyO9' => '',
     *     'key10' => new \stdClass(),
     *     'pastimes' => ['golf', 'opera', 'poker', 'rap'],
     *     'user' => [
     *         'name' => 'Bob Smith',
     *         'age' => 47,
     *         'sex' => 'M',
     *         'dob' => '5/12/1956'
     *     ],
     *     'children' => [
     *         'sally' => ['age' => 8, 'sex' => null],
     *         'bobby' => ['sex' => 'M', 'age' => 12],
     *     ],
     * ];
     * ```
     */
    public static function httpBuildQuery(
        array|object $data,
        string $numericPrefix = '',
        string $argSeparator = '&',
        int $encodingType = \PHP_QUERY_RFC1738
    ): string {
        $toQuery = static function (
            ?string $mainKey,
            mixed $data,
            string $numericPrefix,
            string $argSeparator,
            int $encodingType
        ) use (&$toQuery): string {
            if (!$data instanceof \Traversable) {
                $data = (array) $data;
            }

            reset($data);
            $query = '';

            foreach ($data as $key => $value) {
                // 值处理
                if (null === $value) {
                    continue;
                }

                $value = match ($value) {
                    0,false => '0',
                    default => $value
                };

                // 键处理
                if (null !== $mainKey) {
                    $key = "{$mainKey}[$key]";
                } else {
                    // 为了对数据进行解码时获取合法的变量名
                    is_numeric($key) and !\is_string($key) and $key = $numericPrefix.$key;
                }

                $query .= \is_scalar($value)
                    ? \sprintf(
                        "$argSeparator%s=%s",
                        \PHP_QUERY_RFC3986 === $encodingType ? rawurlencode((string) $key) : urlencode((string) $key),
                        \PHP_QUERY_RFC3986 === $encodingType ? rawurlencode((string) $value) : urlencode((string) $value)
                    )
                    : $toQuery($key, $value, $numericPrefix, $argSeparator, $encodingType); // 递归调用
            }

            return $query;
        };

        return substr($toQuery(null, $data, $numericPrefix, $argSeparator, $encodingType), \strlen($argSeparator));
    }
}
