<?php

namespace App\Support\BitEncoder;

use InvalidArgumentException;
use LengthException;

/**
 * 主要用来处理数据库多个值单字段储存问题。
 *
 * ```php
 * $provinceSet = [
 *     110000,
 *     120000,
 *     130000,
 *     140000,
 *     150000,
 *     210000,
 *     220000,
 *     230000,
 *     310000,
 *     320000,
 *     330000,
 *     340000,
 *     350000,
 *     360000,
 *     370000,
 *     410000,
 *     420000,
 *     430000,
 *     440000,
 *     450000,
 *     460000,
 *     500000,
 *     510000,
 *     520000,
 *     530000,
 *     540000,
 *     610000,
 *     620000,
 *     630000,
 *     640000,
 *     650000
 * ];
 *
 * $bitEncoder = new BitEncoder($provinceSet);
 * $provinces = [110000, 210000, 310000, 410000, 510000, 610000];
 * $bitEncoder->encode($provinces); // 71336225
 * $bitEncoder->decode(71336225); // [110000, 210000, 310000, 410000, 510000, 610000]
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
