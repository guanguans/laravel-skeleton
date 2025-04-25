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

namespace App\Listeners;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Arr;

class TraceEventListener
{
    public function __invoke(string $event): void
    {
        static $clear = false;

        $file = __DIR__.'/../../storage/logs/events.log';

        if (!$clear && is_file($file)) {
            unlink($file);
            $clear = true;
        }

        /**
         * @noinspection DebugFunctionUsageInspection
         */
        $trace = collect(debug_backtrace())
            ->filter(
                static fn (array $trace): bool => isset($trace['file'], $trace['line'])
                    && collect($trace['args'] ?? [])->first(
                        static fn (mixed $arg): bool => $arg === $event
                    )
            )
            ->map(static fn (array $trace) => Arr::except($trace, ['args', 'object']))
            // ->dd()
            ->firstOrFail(static fn (array $trace): bool => !str($trace['file'])->startsWith(
                \dirname((new \ReflectionClass(Dispatcher::class))->getFileName())
            ));

        file_put_contents(
            $file,
            \sprintf(
                '%s [%s:%s]%s',
                $event,
                str($trace['file'])->remove(\dirname(__DIR__, 2))->ltrim('/'),
                $trace['line'],
                \PHP_EOL
            ),
            \FILE_APPEND
        );
    }
}
