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

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PingController extends Controller
{
    public function ping(Request $request, int $isBad = 0): JsonResponse
    {
        $validatedParameters = $request->validate([
            'is_bad' => 'integer',
        ]) + [
            'is_bad' => $isBad,
        ];

        if ($validatedParameters['is_bad']) {
            return $this->apiResponse()->badRequest();
        }

        return $this->apiResponse()->ok();
    }
}
