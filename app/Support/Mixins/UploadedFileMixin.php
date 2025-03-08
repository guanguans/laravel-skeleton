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

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * @see https://github.com/MrPunyapal/basic-crud/blob/main/app/Providers/AppServiceProvider.php
 *
 * @mixin \Illuminate\Http\UploadedFile
 */
#[Mixin(UploadedFile::class)]
class UploadedFileMixin
{
    public static function makeFromUrl(): \Closure
    {
        return static function (string $url): ?UploadedFile {
            $tempFile = tempnam(sys_get_temp_dir(), Str::random(32));

            if (false === $tempFile) {
                return null;
            }

            $file = file_get_contents($url);

            if (false === $file) {
                return null;
            }

            file_put_contents($tempFile, $file);

            return new UploadedFile(
                $tempFile,
                basename($url),
                mime_content_type($tempFile) ?: null,
                null,
                true
            );
        };
    }
}
