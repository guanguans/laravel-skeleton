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
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

final class TraceEventListener
{
    private const string TRIGGER = 'DEBUG_EVENT';

    public function __invoke(string $event): void
    {
        // if (!$this->requestHasTrigger()) {
        //     return;
        // }

        static $clear = false;

        $file = __DIR__.'/../../storage/logs/events.log';

        if (!$clear && is_file($file)) {
            unlink($file);
            $clear = true;
            date_default_timezone_set('Asia/Shanghai');
            file_put_contents($file, Carbon::now()->format("Y-m-d H:i:s\n\n"));
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
            ->sole(static fn (array $trace): bool => !str($trace['file'])->startsWith(
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

    private function requestHasTrigger(): bool
    {
        $request = app()->runningInConsole() ? Request::capture() : resolve(Request::class);

        return false !== getenv(self::TRIGGER)
            || $request->hasHeader(self::TRIGGER)
            || $request->has(self::TRIGGER)
            || $request->server->has(self::TRIGGER)
            || $request->hasCookie(self::TRIGGER);
    }
}
