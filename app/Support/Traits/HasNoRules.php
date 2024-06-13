<?php

declare(strict_types=1);

namespace App\Support\Traits;

trait HasNoRules
{
    final public function rules(): array
    {
        return [];
    }
}
