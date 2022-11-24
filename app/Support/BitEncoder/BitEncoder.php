<?php

namespace App\Support\BitEncoder;

use InvalidArgumentException;
use LengthException;

/**
 * 主要用来处理数据库多个值单字段储存问题。
 *
 * ```
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
 * $bitEncoder->encode($provinces);// 71336225
 * $bitEncoder->decode(71336225); // [110000, 210000, 310000, 410000, 510000, 610000]
 * ```
 */
class BitEncoder implements BitEncoderInterface
{
    /**
     * @var mixed[]
     */
    protected array $set;

    public function __construct(array $set)
    {
        $this->setSet($set);
    }

    public function encode(array $set): int
    {
        $intersectedSet = array_intersect($this->set, $set);

        return array_reduce(array_keys($intersectedSet), function (int $value, int $index) {
            return $value | (1 << $index);
        }, 0);
    }

    public function decode(int $value): array
    {
        $set = [];
        foreach ($this->set as $index => $item) {
            if (($value & (1 << $index)) > 0) {
                $set[] = $item;
            }
        }

        return $set;
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
            throw new InvalidArgumentException('The set is not a unique array of values.');
        }

        if (($count = count($set)) > ($maxCount = PHP_INT_SIZE == 4 ? 31 : 63)) {
            throw new LengthException("The number({$maxCount}) of elements is greater than the maximum length({$count}).");
        }

        $this->set = $set;
    }
}
