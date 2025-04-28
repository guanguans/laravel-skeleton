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

final class PingController extends Controller
{
    public function __invoke(Request $request, bool $bad = false): JsonResponse
    {
        $validatedParameters = $request->validate(['bad' => 'bool']) + ['bad' => $bad];

        if ($validatedParameters['bad']) {
            return $this->apiResponse()->badRequest();
        }

        return $this->apiResponse()->ok();
    }
}
