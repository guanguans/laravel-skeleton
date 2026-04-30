<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Rules;

abstract class AbstractRegexRule extends AbstractRule
{
    #[\Override]
    public function passes(string $attribute, mixed $value): bool
    {
        return (bool) preg_match($this->pattern(), (string) $value);
    }

    /**
     * REGEX pattern of rule.
     *
     * ```
     * [
     *     "BankCardRule" => '/^[1-9]\d{9,29}$/',
     *     "BitcoinAddressRule" => '/^(?:bc1|[13])[a-zA-HJ-NP-Z0-9]{25,39}$/',
     *     "CamelCaseRule" => '/^\p{Lu}?\p{Ll}+(?:\p{Lu}\p{Ll}+)*$/u',
     *     "CapitalCharWithNumberRule" => '/[A-Z]{2,}-\d+/',
     *     "CarNumberRule" => '/^[A-Z]{2}\d{2}[A-Z]{2}\d{4}$/',
     *     "ChineseNameRule" => '/^[\u4e00-\u9fa5·]{2,16}$/',
     *     "ChineseNameRule" => '/^[\x{4e00}-\x{9fa5}·]{2,16}$/u',
     *     "DomainRule" => '/^([\w-]+\.)*[\w\-]+\.\w{2,10}$/',
     *     "EvenNumberRule" => '/^\d*[02468]$/',
     *     "HtmlTagRule" => '/^<([a-z1-6]+)([^<]+)*(?:>(.*)<\/\1>| *\/>)$/',
     *     "IdCardRule" => '/^\d{6}((((((19|20)\d{2})(0[13-9]|1[012])(0[1-9]|[12]\d|30))|(((19|20)\d{2})(0[13578]|1[02])31)|((19|20)\d{2})02(0[1-9]|1\d|2[0-8])|((((19|20)([13579][26]|[2468][048]|0[48]))|(2000))0229))\d{3})|((((\d{2})(0[13-9]|1[012])(0[1-9]|[12]\d|30))|((\d{2})(0[13578]|1[02])31)|((\d{2})02(0[1-9]|1\d|2[0-8]))|(([13579][26]|[2468][048]|0[048])0229))\d{2}))(\d|X|x)$/',
     *     "JwtRule" => '/^[a-zA-Z0-9-_]+\.[a-zA-Z0-9-_]+\.[a-zA-Z0-9-_]+$/',
     *     "KebabCaseRule" => '/^(?:\p{Ll}+-)*\p{Ll}+$/u',
     *     "LocationCoordinatesRule" => '/^-?((([0-8]?\d)(\.(\d{1,8}))?)|(90(\.0+)?)),\s?-?((((1[0-7]\d)|(\d?\d))(\.(\d{1,8}))?)|180(\.0+)?)$/',
     *     "OddNumberRule" => '/^\d*[13579]$/',
     *     "PhoneRule" => '/^(?:(?:\+|00)86)?1[3-9]\d{9}$/',
     *     "PortRule" => '/^((6553[0-5])|(655[0-2]\d)|(65[0-4]\d{2})|(6[0-4]\d{3})|([1-5]\d{4})|([0-5]{0,5})|(\d{1,4}))$/',
     *     "PostalCodeRule" => '/^(0[1-7]|1[0-356]|2[0-7]|3[0-6]|4[0-7]|5[1-7]|6[1-7]|7[0-5]|8[013-6])\d{4}$/',
     *     "SemverRule" => '/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)(?:-((?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\+([0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?$/',
     *     "SlugRule" => '/^[a-z0-9]+(?:-[a-z0-9]+)*$/i',
     *     "SnakeCaseRule" => '/^(?:\p{Ll}+_)*\p{Ll}+$/u',
     *     "WithoutWhitespaceRule" => '/\s/'
     * ]
     * ```
     */
    abstract protected function pattern(): string;
}
