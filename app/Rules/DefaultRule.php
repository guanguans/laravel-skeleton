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

namespace App\Rules;

use App\Rules\Concerns\DataAware;
use App\Rules\Concerns\ValidatorAware;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Arr;

final class DefaultRule extends AbstractRule implements DataAwareRule, ValidatorAwareRule
{
    use DataAware;
    use ValidatorAware;

    /**
     * Indicates whether the rule should be implicit.
     *
     * @noinspection ClassOverridesFieldOfSuperClassInspection
     */
    #[\Override]
    public bool $implicit = true;

    public function __construct(private readonly mixed $default) {}

    /**
     * Determine if the validation rule passes.
     */
    #[\Override]
    public function passes(string $attribute, mixed $value): true
    {
        null === $value and $this->validator->setData(Arr::set($this->data, $attribute, $this->default));

        return true;
    }
}
