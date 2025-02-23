<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

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
