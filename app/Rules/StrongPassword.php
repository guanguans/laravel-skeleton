<?php

namespace App\Rules;

final class StrongPassword extends RegexRule
{
    protected function pattern(): string
    {
        // 最少 8 位，包括至少 1 个大写字母，1 个小写字母，1 个数字，1 个特殊字符
        return '/^\S*(?=\S{8,})(?=\S*\d)(?=\S*[A-Z])(?=\S*[a-z])(?=\S*[!@#$%^&*? ])\S*$/';
    }
}
