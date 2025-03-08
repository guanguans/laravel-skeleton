<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Rules;

use App\Rules\Concerns\DataAware;
use App\Rules\Concerns\ValidatorAware;
use Illuminate\Contracts\Validation\ValidatorAwareRule;

final class DefaultRule extends Rule implements ValidatorAwareRule
{
    // use DataAware;
    use ValidatorAware;

    /** Indicates whether the rule should be implicit. */
    public bool $implicit = true;

    public function __construct(private mixed $default) {}

    /**
     * Determine if the validation rule passes.
     */
    #[\Override]
    public function passes(string $attribute, mixed $value): bool
    {
        if (null === $value) {
            $data = $this->validator->getData();
            $data[$attribute] = $this->default;
            $this->validator->setData($data);
        }

        return true;
    }
}
