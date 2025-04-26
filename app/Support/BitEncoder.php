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

use App\Support\Contracts\BitEncoderContract;

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
final class BitEncoder implements BitEncoderContract
{
    /**
     * 无重复元素的数组.
     *
     * @var list<mixed>
     *
     * @noinspection PropertyAnnotationInspection
     */
    private array $set;

    /**
     * @throws \Throwable
     */
    public function __construct(array $set)
    {
        $this->setSet($set);
    }

    /**
     * 编码.
     *
     * @throws \InvalidArgumentException
     * @throws \Throwable
     */
    public function encode(array $set): int
    {
        return $this->attach(0, ...$set);
    }

    /**
     * 解码.
     *
     * @throws \InvalidArgumentException
     * @throws \Throwable
     */
    public function decode(int $value): array
    {
        return array_filter($this->set, fn ($item): bool => $this->has($value, $item));
    }

    /**
     * 附加.
     *
     * @throws \InvalidArgumentException
     * @throws \Throwable
     */
    public function attach(int $value, ...$set): int
    {
        throw_if(0 > $value, \InvalidArgumentException::class, "The value [$value] is an invalid positive integer.");

        return array_reduce(
            $set,
            function (int $value, $item): int {
                $index = array_search($item, $this->set, true);

                if (false !== $index) {
                    $value |= (1 << $index);
                }

                return $value;
            },
            $value
        );
    }

    /**
     * 移除.
     *
     * @throws \InvalidArgumentException
     * @throws \Throwable
     */
    public function detach(int $value, ...$set): int
    {
        throw_if(0 > $value, \InvalidArgumentException::class, "The value [$value] is an invalid positive integer.");

        return array_reduce(
            $set,
            function (int $value, $item): int {
                $index = array_search($item, $this->set, true);

                if (false !== $index) {
                    $value &= (~(1 << $index));
                }

                return $value;
            },
            $value
        );
    }

    /**
     * 包含.
     *
     * @throws \InvalidArgumentException
     * @throws \Throwable
     */
    public function has(int $value, mixed $item): bool
    {
        throw_if(0 > $value, \InvalidArgumentException::class, "The value [$value] is an invalid positive integer.");

        $index = array_search($item, $this->set, true);

        if (false === $index) {
            return false;
        }

        $bit = 1 << $index;

        return ($value & $bit) === $bit;
    }

    /**
     * 缺少.
     *
     * @throws \InvalidArgumentException
     * @throws \Throwable
     */
    public function lack(int $value, mixed $item): bool
    {
        throw_if(0 > $value, \InvalidArgumentException::class, "The value [$value] is an invalid positive integer.");

        $index = array_search($item, $this->set, true);

        if (false === $index) {
            return true;
        }

        return 0 === ($value & (1 << $index));
    }

    /**
     * 获取包含该集合的所有组合的编码值.
     *
     * @throws \Throwable
     */
    public function getHasCombinationsValues(array $set, int $length = 1024): array
    {
        return array_map(fn (array $set): int => $this->encode($set), $this->getHasCombinations($set, $length));
    }

    /**
     * 获取缺少该集合的所有组合的编码值.
     *
     * @throws \Throwable
     */
    public function getLackCombinationsValues(array $set, int $length = 1024): array
    {
        return array_map(fn (array $set): int => $this->encode($set), $this->getLackCombinations($set, $length));
    }

    /**
     * 获取包含该集合的所有组合.
     */
    public function getHasCombinations(array $set, int $length = 1024): array
    {
        $combinationsCount = $this->getHasCombinationsCount($set);

        if ($combinationsCount > $length) {
            trigger_error('Did not get all has combinations.');
        }

        $combinations = [];

        foreach ($this->getHasCombinationsGenerator($set) as $index => $combination) {
            if (0 <= $length && $index >= $length) {
                break;
            }

            $combinations[] = $combination;
        }

        return $combinations;
    }

    /**
     * 获取缺少该集合的所有组合.
     */
    public function getLackCombinations(array $set, int $length = 1024): array
    {
        $combinationsCount = $this->getLackCombinationsCount($set);

        if ($combinationsCount > $length) {
            trigger_error('Did not get all lack combinations.');
        }

        $combinations = [];

        foreach ($this->getLackCombinationsGenerator($set) as $index => $combination) {
            if (0 <= $length && $index >= $length) {
                break;
            }

            $combinations[] = $combination;
        }

        return $combinations;
    }

    /**
     * 获取包含该集合的所有组合的生成器.
     */
    public function getHasCombinationsGenerator(array $set): \Generator
    {
        $set = array_intersect($this->set, $set);

        if ([] === $set) {
            return; // 中断
        }

        $subSetCount = \count($set);
        $setCount = \count($this->set);

        for ($length = $subSetCount; $length <= $setCount; ++$length) {
            foreach ($this->combinationGenerator($this->set, $length) as $combination) {
                if (array_values(array_intersect($combination, $set)) === array_values($set)) {
                    yield $combination;
                }
            }
        }
    }

    /**
     * 获取缺少该集合的所有组合的生成器.
     */
    public function getLackCombinationsGenerator(array $set): \Generator
    {
        $set = array_intersect($this->set, $set);

        if ([] === $set) {
            return; // 中断
        }

        $subSetCount = \count($set);
        $setCount = \count($this->set);

        for ($length = 1; $length <= $setCount; ++$length) {
            foreach ($this->combinationGenerator($this->set, $length) as $combination) {
                if ($length < $subSetCount) {
                    yield $combination;

                    continue;
                }

                if (array_values(array_intersect($combination, $set)) !== array_values($set)) {
                    yield $combination;
                }
            }
        }
    }

    /**
     * 获取包含该集合的所有组合的数量.
     */
    public function getHasCombinationsCount(array $set): int
    {
        if (0 === ($subSetCount = \count(array_intersect($this->set, $set)))) {
            return 0;
        }

        return 2 ** (\count($this->set) - $subSetCount);
    }

    /**
     * 获取缺少该集合的所有组合的数量.
     */
    public function getLackCombinationsCount(array $set): int
    {
        if ([] === array_intersect($this->set, $set)) {
            return 0;
        }

        // ((2 ** n) - 1) - (2 ** (n - m))
        // ((2 ** n) - 1) - (2 ** n ) / (2 ** m)

        return $this->getCombinationsCount() - $this->getHasCombinationsCount($set);
    }

    /**
     * 获取所有组合的数量.
     */
    public function getCombinationsCount(): int
    {
        return 2 ** \count($this->set) - 1;
    }

    public function getSet(): array
    {
        return $this->set;
    }

    /**
     * @throws \Throwable
     */
    public function setSet(array $set): void
    {
        throw_unless(array_is_list($set), \InvalidArgumentException::class, 'The set is not an array of lists.');

        throw_if(
            array_filter(array_count_values($set), static fn (int $count): bool => 1 < $count),
            \InvalidArgumentException::class,
            'The set must be an array with no duplicate elements.'
        );

        if (($count = \count($set)) > ($maxCount = \PHP_INT_SIZE === 4 ? 31 : 63)) {
            throw new \LengthException("The number [$maxCount] of elements is greater than the maximum length [$count].");
        }

        $this->set = $set;
    }

    /**
     * 组合生成器.
     */
    private function combinationGenerator(array $set, int $length): \Generator
    {
        $originalLength = \count($set);
        $remainingLength = $originalLength - $length + 1;

        for ($index = 0; $index < $remainingLength; ++$index) {
            $current = $set[$index];

            if (1 === $length) {
                yield [$current];
            } else {
                $remaining = \array_slice($set, $index + 1);

                foreach ($this->combinationGenerator($remaining, $length - 1) as $permutation) {
                    array_unshift($permutation, $current);

                    yield $permutation;
                }
            }
        }
    }
}
