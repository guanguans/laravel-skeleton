<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Rules;

use App\Rules\Concerns\DataAware;
use App\Rules\Concerns\ValidatorAware;
use Illuminate\Contracts\Validation\ValidatorAwareRule;

final class DefaultRule extends Rule implements ValidatorAwareRule
{
    // use DataAware;
    use ValidatorAware;

    /**
     * Indicates whether the rule should be implicit.
     */
    public bool $implicit = true;

    public function __construct(private mixed $default) {}

    /**
     * Determine if the validation rule passes.
     */
    #[\Override]
    public function passes(string $attribute, mixed $value): bool
    {
        if ($value === null) {
            $data = $this->validator->getData();
            $data[$attribute] = $this->default;
            $this->validator->setData($data);
        }

        return true;
    }
}
