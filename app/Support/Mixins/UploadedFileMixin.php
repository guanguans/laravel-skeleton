<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Mixins;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * @see https://github.com/MrPunyapal/basic-crud/blob/main/app/Providers/AppServiceProvider.php
 *
 * @mixin \Illuminate\Http\UploadedFile
 */
class UploadedFileMixin
{
    public static function makeFromUrl(): \Closure
    {
        return static function (string $url): ?UploadedFile {
            $tempFile = tempnam(sys_get_temp_dir(), Str::random(32));
            if ($tempFile === false) {
                return null;
            }

            $file = file_get_contents($url);
            if ($file === false) {
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
