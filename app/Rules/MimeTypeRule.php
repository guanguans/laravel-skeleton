<?php

namespace App\Rules;

final class MimeTypeRule extends RegexRule
{
    protected function pattern(): string
    {
        /** @lang PhpRegExp */
        return "/^(multipart|application|audio|image|message|text|video|font|example|model)\/([-+.\w]+)$/i";
    }
}
