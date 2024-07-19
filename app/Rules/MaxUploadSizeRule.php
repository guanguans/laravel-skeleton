<?php

namespace App\Rules;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

/**
 * @see https://github.com/antoninmasek/laravel-max-upload-size-rule
 * @see https://tonymasek.com/blog/easily-validate-max-file-size-based-on-your-settings-in-phpini
 *
 * @todo
 */
final class MaxUploadSizeRule extends Rule
{
    public function __construct()
    {
        /**
         * This method is here in order to mock calls to `ini_get` when testing.
         */
        App::macro('getPhpIniValue', function (string $key): false|string {
            return \ini_get($key);
        });

        File::macro('maxUploadSize', function (): \Illuminate\Contracts\Validation\Rule {
            $uploadMaxSizeInBytes = ini_parse_quantity(App::getPhpIniValue('upload_max_filesize'));
            if ($uploadMaxSizeInBytes > 0) {
                return $this->max($uploadMaxSizeInBytes / 1024);
            }

            $postMaxSizeInBytes = ini_parse_quantity(App::getPhpIniValue('post_max_size'));
            if ($postMaxSizeInBytes > 0) {
                return $this->max($postMaxSizeInBytes / 1024);
            }

            return $this;
        });
    }

    public function passes(string $attribute, mixed $value): bool
    {
        return Hash::check($value, auth()->user()?->getAuthPassword());
    }

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        $rule = \Illuminate\Validation\Rule::file()->maxUploadSize();

        $validator = Validator::make(
            [$attribute => $value],
            [$attribute => $rule]
        );

        if ($validator->fails()) {
            $fail(Arr::first($rule->message()));
        }
    }
}
