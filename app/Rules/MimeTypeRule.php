<?php

namespace App\Rules;

final class MimeTypeRule extends RegexRule
{
    protected function pattern(): string
    {
        return "/^(multipart|application|audio|image|message|text|video|font|example|model)\/([-+.\w]+)$/i";
    }
}
