<?php

namespace App\Rules;

final class StrongPassword extends RegexRule
{
    protected function pattern(): string
    {
        // 最少6位，包括至少1个大写字母，1个小写字母，1个数字，1个特殊字符
        return '/^\S*(?=\S{8,})(?=\S*\d)(?=\S*[A-Z])(?=\S*[a-z])(?=\S*[!@#$%^&*? ])\S*$/';
    }
}
