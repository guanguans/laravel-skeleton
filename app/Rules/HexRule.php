<?php

namespace App\Rules;

final class HexRule extends RegexRule
{
    /**
     * @var bool
     */
    protected $forceFull;

    /**
     * @var bool
     */
    protected $allowAlpha;

    public function __construct(bool $forceFull = false, bool $allowAlpha = false)
    {
        $this->forceFull = $forceFull;
        $this->allowAlpha = $allowAlpha;
    }

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

        $pattern .= ')$/';

        return $pattern;
    }
}
