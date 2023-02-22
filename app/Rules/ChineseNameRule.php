<?php

namespace App\Rules;

final class ChineseNameRule extends RegexRule
{
    /**
     * 中文姓名.
     */
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return '/^(?:[\u4e00-\u9fa5·]{2,16})$/';
    }
}
