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

use Illuminate\Support\Facades\Validator;

/**
 * @see https://github.com/antoninmasek/laravel-max-upload-size-rule
 * @see https://tonymasek.com/blog/easily-validate-max-file-size-based-on-your-settings-in-phpini
 */
final class MaxUploadSizeRule extends Rule
{
    public function passes(string $attribute, mixed $value): bool
    {
        if (($maxUploadSizeInBytes = $this->maxUploadSize()) < 0) {
            return true;
        }

        return Validator::make(
            [$attribute => $value],
            [$attribute => \Illuminate\Validation\Rule::file()->max($maxUploadSizeInBytes / 1024)]
        )->passes();
    }

    private function maxUploadSize(): int
    {
        // We know, that `upload_max_filesize` has to have a lower value
        // than `post_max_size`. So in case there is a limit set
        // we should use this because it can't be higher than `post_max_size`
        $uploadMaxSize = ini_parse_quantity(\ini_get('upload_max_filesize'));
        if ($uploadMaxSize > 0) {
            return $uploadMaxSize;
        }

        // In case `upload_max_filesize` is 0 or lower, it means, that
        // the limit is disabled. In that case, check for the value of `post_max_size`
        $postMaxSize = ini_parse_quantity(\ini_get('post_max_size'));
        if ($postMaxSize > 0) {
            return $postMaxSize;
        }

        // Limit is disabled
        return -1;
    }
}
