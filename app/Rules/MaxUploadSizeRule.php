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

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @see https://github.com/antoninmasek/laravel-max-upload-size-rule
 * @see https://tonymasek.com/blog/easily-validate-max-file-size-based-on-your-settings-in-phpini
 */
final class MaxUploadSizeRule extends AbstractRule
{
    #[\Override]
    public function passes(string $attribute, mixed $value): bool
    {
        if (0 > ($maxUploadSizeInBytes = $this->maxUploadSize())) {
            return true;
        }

        return Validator::make(
            [$attribute => $value],
            [$attribute => Rule::file()->max($maxUploadSizeInBytes / 1024)]
        )->passes();
    }

    private function maxUploadSize(): int
    {
        // We know, that `upload_max_filesize` has to have a lower value
        // than `post_max_size`. So in case there is a limit set
        // we should use this because it can't be higher than `post_max_size`
        $uploadMaxSize = ini_parse_quantity(\ini_get('upload_max_filesize'));

        if (0 < $uploadMaxSize) {
            return $uploadMaxSize;
        }

        // In case `upload_max_filesize` is 0 or lower, it means, that
        // the limit is disabled. In that case, check for the value of `post_max_size`
        $postMaxSize = ini_parse_quantity(\ini_get('post_max_size'));

        if (0 < $postMaxSize) {
            return $postMaxSize;
        }

        // Limit is disabled
        return -1;
    }
}
