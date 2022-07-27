<?php

namespace App\Rules;

final class UrlRule extends RegexRule
{
    protected function pattern(): string
    {
        return '/^(((ht|f)tps?):\/\/)?([^!@#$%^&*?.\s-]([^!@#$%^&*?.\s]{0,63}[^!@#$%^&*?.\s])?\.)+[a-z]{2,6}\/?/';
    }
}
