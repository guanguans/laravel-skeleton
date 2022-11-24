<?php

namespace App\Support\BitEncoder;

use InvalidArgumentException;
use LengthException;

/**
 * 主要用来处理数据库多个值单字段储存问题。
 *
 * ```php
 * $provinceSet = [11,12,13,14,15,21,22,23,31,32,33,34,35,36,37,41,42,43,44,45,46,50,51,52,53,54,61,62,63,64,65];
 * $bitEncoder = new BitEncoder($provinceSet);
 * $provinces = [11, 21, 31, 41, 51, 61];
 * $bitEncoder->encode($provinces); // 71336225
 * $bitEncoder->decode(71336225); // [11, 21, 31, 41, 51, 61]
 * ```
 */
class BitEncoder implements BitEncoderInterface
{
    /**
     * 无重复元素的数组.
     *
     * @var mixed[]
     */
    protected array $set;

    public function __construct(array $set)
    {
        $this->setSet($set);
    }

    /**
     * 编码.
     *
     * @throws \InvalidArgumentException
     */
    public function encode(array $set): int
    {
        return $this->attach(0, ...$set);
    }

    /**
     * 解码.
     *
     * @throws \InvalidArgumentException
     */
    public function decode(int $value): array
    {
        return array_filter($this->set, fn ($item) => $this->has($value, $item));
    }

    /**
     * 附加.
     *
     * @throws \InvalidArgumentException
     */
    public function attach(int $value, ...$set): int
    {
        if ($value < 0) {
            throw new InvalidArgumentException("The value($value) is an invalid positive integer.");
        }

        return array_reduce($set, function (int $value, $item) {
            $index = array_search($item, $this->set, true);
            if ($index !== false) {
                $value |= (1 << $index);
            }

            return $value;
        }, $value);
    }

    /**
     * 移除.
     *
     * @throws \InvalidArgumentException
     */
    public function detach(int $value, ...$set): int
    {
        if ($value < 0) {
            throw new InvalidArgumentException("The value($value) is an invalid positive integer.");
        }

        return array_reduce($set, function (int $value, $item) {
            $index = array_search($item, $this->set, true);
            if ($index !== false) {
                $value &= (~(1 << $index));
            }

            return $value;
        }, $value);
    }

    /**
     * 拥有.
     *
     * @throws \InvalidArgumentException
     */
    public function has(int $value, $item): bool
    {
        if ($value < 0) {
            throw new InvalidArgumentException("The value($value) is an invalid positive integer.");
        }

        $index = array_search($item, $this->set, true);
        if ($index === false) {
            return false;
        }

        $bit = 1 << $index;

        return ($value & $bit) === $bit;
    }

    /**
     * 缺少.
     *
     * @throws \InvalidArgumentException
     */
    public function lack(int $value, $item): bool
    {
        if ($value < 0) {
            throw new InvalidArgumentException("The value($value) is an invalid positive integer.");
        }

        $index = array_search($item, $this->set, true);
        if ($index === false) {
            return true;
        }

        return ($value & (1 << $index)) === 0;
    }

    public function getSet(): array
    {
        return $this->set;
    }

    public function setSet(array $set): void
    {
        if (! array_is_list($set)) {
            throw new InvalidArgumentException('The set is not an array of lists.');
        }

        if (array_filter(array_count_values($set), fn (int $count) => $count > 1)) {
            throw new InvalidArgumentException('The set must be an array with no duplicate elements.');
        }

        if (($count = count($set)) > ($maxCount = PHP_INT_SIZE == 4 ? 31 : 63)) {
            throw new LengthException("The number({$maxCount}) of elements is greater than the maximum length({$count}).");
        }

        $this->set = $set;
    }
}
