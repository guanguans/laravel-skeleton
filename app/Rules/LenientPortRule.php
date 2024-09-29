<?php

namespace App\Rules;

final class LenientPortRule extends AggregateRule
{
    protected function rules(): array
    {
        return [
            'int',
            'between:1,65535',
        ];
    }
}
