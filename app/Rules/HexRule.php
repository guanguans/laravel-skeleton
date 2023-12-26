<?php

namespace App\Rules;

final class HexRule extends RegexRule
{
    public function __construct(protected bool $forceFull = false, protected bool $allowAlpha = false) {}

    protected function pattern(): string
    {
        $pattern = '/^#([a-fA-F0-9]{6}';

        if (! $this->forceFull) {
            $pattern .= '|[a-fA-F0-9]{3}';
        }

        if ($this->allowAlpha) {
            $pattern .= '|[a-fA-F0-9]{8}';

            if (! $this->forceFull) {
                $pattern .= '|[a-fA-F0-9]{4}';
            }
        }

        return $pattern.')$/';
    }
}
