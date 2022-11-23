<?php

namespace App\Support\BitEncoder;

use InvalidArgumentException;

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
    private array $set;

    public function __construct(array $set)
    {
        if (! array_is_list($set)) {
            throw new InvalidArgumentException(sprintf('The set is not list array.'));
        }

        $maxCount = (PHP_INT_SIZE == 4 ? 32 : 64) - 1;
        if (($count = count($set)) > $maxCount) {
            throw new InvalidArgumentException(sprintf('The maximum number of elements is %s.:%s', $maxCount, $count));
        }

        $this->set = $set;
    }

    public function encode(array $set): int
    {
        $intersectSetIndices = array_keys(array_intersect($this->set, $set));

        return array_reduce($intersectSetIndices, function (int $value, int $index) {
            return $value | (2 ** $index);
        }, 0);
    }

    public function decode(int $value): array
    {
        $set = [];
        foreach ($this->set as  $index => $item) {
            if (($value & (2 ** $index)) > 0) {
                $set[] = $item;
            }
        }

        return $set;
    }
}
