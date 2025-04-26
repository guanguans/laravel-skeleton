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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * @mixin \Illuminate\Http\UploadedFile
 */
#[Mixin(UploadedFile::class)]
final class UploadedFileMixin
{
    /**
     * @see https://github.com/MrPunyapal/basic-crud/blob/main/app/Support/FileUploaderFromUrl.php
     * @see https://github.com/MrPunyapal/basic-crud/blob/main/app/Http/Requests
     */
    public static function makeFromUrl(): \Closure
    {
        return static function (string $url): ?UploadedFile {
            $response = Http::get($url);

            if ($response->failed()) {
                return null;
            }

            $tempFile = sys_get_temp_dir().\DIRECTORY_SEPARATOR.Str::uuid()->toString();

            File::put($tempFile, $response->body());

            return new UploadedFile(
                $tempFile,
                File::basename($url),
                $response->header('Content-Type') ?: null,
                null,
                true
            );
        };
    }
}
