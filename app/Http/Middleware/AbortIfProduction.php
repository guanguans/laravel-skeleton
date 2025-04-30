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

namespace App\Http\Middleware;

use App\Support\Traits\WithPipeArgs;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

final class AbortIfProduction extends AbortIf
{
    use WithPipeArgs;

    #[\Override]
    protected function when(): bool
    {
        return App::isProduction();
    }

    #[\Override]
    protected function code(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}
