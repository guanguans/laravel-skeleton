<?php

namespace App\Rules;

class MimeTypeRule extends Rule
{
    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;

        return preg_match("/^(multipart|application|audio|image|message|multipart|text|video|font|example|model)\/([-+.\w]+)$/i", $value) > 0;
    }
}
