<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Pion\Laravel\ChunkUpload\Handler\ResumableJSUploadHandler;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class ChunkUploadController extends Controller
{
    /**
     * @see https://github.com/pionl/laravel-chunk-upload
     * @see https://www.youtube.com/watch?v=Me3-o57Cprc
     *
     * @noinspection NativeMemberUsageInspection
     *
     * @throws \Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $receiver = new FileReceiver($request->post('file'), $request, ResumableJSUploadHandler::class);
        $save = $receiver->receive();

        if ($save->isFinished()) {
            /** @var \Illuminate\Http\UploadedFile $file */
            $file = $save->getFile();
            $newFileName = $file->hashName();
            $file->move(storage_path('app/chunks'), $newFileName);
        }

        $handler = $save->handler();

        return response()->json(['progress' => $handler->getPercentageDone()]);
    }
}