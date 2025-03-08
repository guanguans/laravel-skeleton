<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support;

/**
 * Inspect PHP values.
 *
 * @see https://github.com/coralsio/laraship/blob/main/Corals/core/Foundation/Formatter/Value.php
 */
final class Value
{
    /** @see https://php.net/types.array */
    public const string TYPE_ARRAY = 'array';

    /** @see https://php.net/types.boolean */
    public const string TYPE_BOOL = 'boolean';

    /** @see https://php.net/types.callable */
    public const string TYPE_CALLABLE = 'callable';

    /** @see https://php.net/types.resource */
    public const string TYPE_CLOSED_RESOURCE = 'closed resource';

    /** @see https://php.net/types.float */
    public const string TYPE_FLOAT = 'float';

    /** @see https://php.net/types.integer */
    public const string TYPE_INT = 'integer';

    /** @see https://php.net/types.iterable */
    public const string TYPE_ITERABLE = 'iterable';

    /** @see https://php.net/types.null */
    public const string TYPE_NULL = 'null';

    /** @see https://php.net/types.object */
    public const string TYPE_OBJECT = 'object';

    /** @see https://php.net/types.resource */
    public const string TYPE_RESOURCE = 'resource';

    /** @see https://php.net/types.string */
    public const string TYPE_STRING = 'string';

    /**
     * Final abstract class.
     */
    private function __construct() {}

    /**
     * Get a human-readable description of the given value’s type as used by
     * PHP itself in (error) messages.
     *
     * PHP uses a highly inconsistent naming when it comes to types. This is not
     * only true for functions like {@see \gettype} but also for the messages
     * that are produced by PHP; not to mention the confusion in the C code.
     *
     * The type names used in this method are consistent with the ones used in
     * PHP 7’s {@see \TypeError} messages which I believe are the ones that
     * most developers will see in the future and get most used to. This also
     * means that the full class name is returned for objects instead of simply
     * _object_.
     *
     * Another special edge case which this method handles properly is the case
     * of closed resources which are reported as _unknown type_ by
     * {@see \gettype} which might cast some confusion in certain situations.
     * This method will return _closed resource_ in such situations.
     *
     * Any unknown type will result in the string _unknown_, however, it should
     * not be possible that this happens and a bug should be filed against PHP
     * if this is encountered.
     *
     * @return string
     *                Human-readable name for the type of the given value
     *
     * @see \get_class()
     * @see \gettype()
     */
    public static function getType(mixed $value): string
    {
        if (null === $value) {
            return self::TYPE_NULL;
        }

        if (\is_array($value)) {
            return self::TYPE_ARRAY;
        }

        if (\is_bool($value)) {
            return self::TYPE_BOOL;
        }

        if (\is_float($value)) {
            return self::TYPE_FLOAT;
        }

        if (\is_int($value)) {
            return self::TYPE_INT;
        }

        if (\is_object($value)) {
            return $value::class;
        }

        if (\is_string($value)) {
            return self::TYPE_STRING;
        }

        if (\is_resource($value)) {
            return self::TYPE_RESOURCE;
        }

        if (get_resource_type($value) === 'Unknown') {
            return self::TYPE_CLOSED_RESOURCE;
        }

        // @codeCoverageIgnoreStart
        // It should not be possible to reach this point since the above covers
        // all known PHP types, hence, there is no way to test this branch.
        return 'unknown';
        // @codeCoverageIgnoreEnd
    }
}
