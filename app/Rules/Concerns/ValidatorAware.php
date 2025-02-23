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

use Illuminate\Validation\Validator;

trait ValidatorAware
{
    protected Validator $validator;

    /**
     * Set the current validator.
     */
    public function setValidator(Validator $validator): self
    {
        $this->validator = $validator;

        return $this;
    }
}
