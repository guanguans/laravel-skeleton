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
 * @see https://github.com/godruoyi/laravel-idcard-validator
 */
class IdCard
{
    /**
     * 允许的地区码
     */
    private static array $areaCodes = [
        11 => '北京',
        12 => '天津',
        13 => '河北',
        14 => '山西',
        15 => '内蒙古',
        21 => '辽宁',
        22 => '吉林',
        23 => '黑龙江',
        31 => '上海',
        32 => '江苏',
        33 => '浙江',
        34 => '安徽',
        35 => '福建',
        36 => '江西',
        37 => '山东',
        41 => '河南',
        42 => '湖北',
        43 => '湖南',
        44 => '广东',
        45 => '广西',
        46 => '海南',
        50 => '重庆',
        51 => '四川',
        52 => '贵州',
        53 => '云南',
        54 => '西藏',
        61 => '陕西',
        62 => '甘肃',
        63 => '青海',
        64 => '宁夏',
        65 => '新疆',
        81 => '香港',
        82 => '澳门',
        83 => '台湾',
    ];

    /**
     * 安全码
     */
    private static array $securityCodes = [
        1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2,
    ];

    /**
     * Determine if the given idcard is passed.
     */
    public static function passes(mixed $idcard): bool
    {
        if (! preg_match('/^'.self::resolveMatchRule().'$/', $idcard)) {
            return false;
        }

        $areaCode = substr($idcard, 0, 2);
        if (! isset(self::$areaCodes[$areaCode])) {
            return false;
        }

        $month = (int) substr($idcard, 10, 2);
        $day = (int) substr($idcard, 12, 2);
        $year = (int) substr($idcard, 6, 4);

        if (! checkdate($month, $day, $year)) {
            return false;
        }

        $sum = 0;

        for ($i = 17; $i > 0; --$i) {
            $s = 2 ** $i % 11;

            $sum += $s * $idcard[17 - $i];
        }

        return (int) self::$securityCodes[$sum % 11] === (int) $idcard[17];
    }

    /**
     * Resolve IdCard Validate Rule.
     *
     * like '[1-6|8]{1}\d{5}[19|20]{2}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9xX]{1}'
     */
    public static function resolveMatchRule(): string
    {
        $rule = '';

        foreach (['areacode', 'years', 'months', 'days', 'randoms', 'checkcode'] as $method) {
            $method = \sprintf('resolveMatchRuleFor%s', ucfirst($method));
            $rule .= self::$method();
        }

        return $rule;
    }

    /**
     * 地址码规则：.
     *
     *     地址码长 6 位
     *     以数字 1-9 开头
     *     后 5 位为 0-9 的数字
     */
    public static function resolveMatchRuleForAreaCode(): string
    {
        return '[1-9]\d{5}';
    }

    /**
     * 年份码规则：.
     *
     *    年份码长 4 位
     *    以数字 18，19 或 20 开头，分表表示 18xx 19xx 20xx
     *    剩余两位为 0-9 的数字
     */
    public static function resolveMatchRuleForYears(): string
    {
        return '(17|18|19|20|21)\d{2}';
    }

    /**
     * 月份码规则：.
     *
     *     月份码长 2 位
     *     第一位数字为 0，第二位数字为 1-9 即 01 02 等月份
     *
     *     或者 10 11 12 月份
     */
    public static function resolveMatchRuleForMonths(): string
    {
        return '((0[1-9])|(10|11|12))';
    }

    /**
     * 日期码规则：.
     *
     *    日期码长2位
     *    第一位数字为0-2，第二位数字为1-9 即 01 22 11 等
     *    或者是10，20，30，31
     */
    public static function resolveMatchRuleForDays(): string
    {
        return '(([0-2][1-9])|10|20|30|31)';
    }

    /**
     * 随机码规则：.
     *
     *     随机码长 3 位
     *     随机码是数字
     */
    public static function resolveMatchRuleForRandoms(): string
    {
        return '\d{3}';
    }

    /**
     * 校验码规则：.
     *
     *    校验码长1位
     *    可以是数字，字母x或字母X
     */
    public static function resolveMatchRuleForCheckCode(): string
    {
        return '[0-9xX]{1}';
    }
}
