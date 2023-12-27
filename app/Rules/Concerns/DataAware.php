<?php

namespace App\Rules\Concerns;

trait DataAware
{
    protected array $data;

    /**
     * Set the data under validation.
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
