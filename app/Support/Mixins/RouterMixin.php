<?php

/** @noinspection PhpMethodParametersCountMismatchInspection */
/** @noinspection PhpIncompatibleReturnTypeInspection */

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
use Illuminate\Routing\Router;

/**
 * @mixin \Illuminate\Routing\Router
 */
#[Mixin(Router::class)]
final class RouterMixin
{
    /**
     * Load routes from multiple files.
     *
     * @see https://github.com/kirschbaum-development/laravel-route-file-macro
     */
    public function files(): \Closure
    {
        return function (array $files): void {
            collect($files)->each(fn (\SplFileInfo|string $file): Router => $this->file($file));
        };
    }

    /**
     * Load routes from a file.
     *
     * @see https://github.com/kirschbaum-development/laravel-route-file-macro
     */
    public function file(): \Closure
    {
        return fn (\SplFileInfo|string $file): Router => $this->group(
            [],
            ($file instanceof \SplFileInfo) ? $file->getRealPath() : $file
        );
    }
}
