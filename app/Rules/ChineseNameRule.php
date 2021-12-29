<?php

namespace App\Rules;

final class ChineseNameRule extends RegexRule
{
    /**
     * 中文姓名.
     *
     * @return string
     */
    protected function pattern(): string
    {
        return '/^(?:[\u4e00-\u9fa5·]{2,16})$/';
    }
}
