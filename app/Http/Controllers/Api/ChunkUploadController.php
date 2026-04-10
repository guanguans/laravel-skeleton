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

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Handler\ResumableJSUploadHandler;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

final class ChunkUploadController extends Controller
{
    /**
     * @see https://github.com/pionl/laravel-chunk-upload
     * @see https://www.youtube.com/watch?v=Me3-o57Cprc
     *
     * @throws \Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException
     *
     * @noinspection NativeMemberUsageInspection
     * @noinspection PhpRedundantVariableDocTypeInspection
     */
    public function __invoke(Request $request): JsonResponse
    {
        $fileReceiver = new FileReceiver($request->post('file'), $request, ResumableJSUploadHandler::class);
        $save = $fileReceiver->receive();

        if ($save->isFinished()) {
            $file = $save->getFile();
            \assert($file instanceof UploadedFile);
            $newFileName = $file->hashName();
            $file->move(storage_path('app/chunks'), $newFileName);
        }

        $handler = $save->handler();

        return new JsonResponse(['progress' => $handler->getPercentageDone()]);
    }
}
