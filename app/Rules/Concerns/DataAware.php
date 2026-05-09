<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Rules\Concerns;

trait DataAware
{
    /** @var array<string, mixed> */
    protected array $data;

    /**
     * Set the data under validation.
     *
     * @see \Illuminate\Contracts\Validation\DataAwareRule
     *
     * {@inheritDoc}
     *
     * @param array<string, mixed> $data
     */
    #[\Override]
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
