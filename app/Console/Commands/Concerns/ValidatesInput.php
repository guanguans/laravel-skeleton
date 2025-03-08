<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Console\Commands\Concerns;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The trait to validate console commands input.
 *
 * @see https://github.com/cerbero90/command-validator/blob/develop/src/ValidatesInput.php
 *
 * @mixin \Illuminate\Console\Command
 */
trait ValidatesInput
{
    protected ValidatorContract $validator;

    abstract protected function rules(): array;

    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        throw_if($this->validator()->fails(), new InvalidArgumentException($this->errors()));

        return parent::execute($input, $output);
    }

    protected function validator(): ValidatorContract
    {
        return $this->validator ?? $this->validator = Validator::make(
            $this->input(),
            $this->rules(),
            $this->messages(),
            $this->attributes()
        );
    }

    protected function errors(): string
    {
        return implode(PHP_EOL, $this->validator()->errors()->all());
    }

    protected function input(): array
    {
        return array_filter(
            array_merge($this->argument(), $this->option()),
            static fn ($value): bool => $value !== null
        );
    }

    protected function messages(): array
    {
        return [];
    }

    protected function attributes(): array
    {
        return [];
    }
}
