<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Rules;

final class PhoneCisRule extends RegexRule
{
    #[\Override]
    protected function pattern(): string
    {
        return /** @lang PhpRegExp */ '/^((\+?7|8)(?!95[4-79]|99[08]|907|94[^0]|336)([348]\d|9[0-6789]|7[0247])\d{8}|\+?(99[^4568]\d{7,11}|'
                                 .'994\d{9}|9955\d{8}|996[57]\d{8}|9989\d{8}|380[34569]\d{8}|375[234]\d{8}|372\d{7,8}|37[0-4]\d{8}))$/';
    }
}
