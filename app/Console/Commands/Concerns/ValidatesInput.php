<?php

namespace App\Console\Commands\Concerns;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The trait to validate console commands input.
 *
 * @mixin \Illuminate\Console\Command
 */
trait ValidatesInput
{
    /**
     * The command input validator.
     *
     * @var \Illuminate\Contracts\Validation\Validator
     */
    protected $validator;

    /**
     * Retrieve the rules to validate data against
     */
    abstract protected function rules(): array;

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->validator()->fails()) {
            throw new InvalidArgumentException($this->formatErrors());
        }

        return parent::execute($input, $output);
    }

    /**
     * Retrieve the command input validator
     */
    protected function validator(): ValidatorContract
    {
        if (isset($this->validator)) {
            return $this->validator;
        }

        return $this->validator = Validator::make(
            $this->getDataToValidate(),
            $this->rules(),
            $this->messages(),
            $this->attributes()
        );
    }

    /**
     * Retrieve the data to validate
     */
    protected function getDataToValidate(): array
    {
        $data = array_merge($this->argument(), $this->option());

        return array_filter($data, function ($value) {
            return $value !== null;
        });
    }

    /**
     * Format the validation errors
     */
    protected function formatErrors(): string
    {
        return implode(PHP_EOL, $this->validator()->errors()->all());
    }

    /**
     * Retrieve the custom error messages
     */
    protected function messages(): array
    {
        return [];
    }

    /**
     * Retrieve the custom attribute names for error messages
     */
    protected function attributes(): array
    {
        return [];
    }
}
