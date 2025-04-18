<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

use App\Support\Facades\PushDeer;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    Carbon::setTestNow(now());
    Http::preventStrayRequests();
});

it('can push message', function (): void {
    Http::fake([
        '*://api2.pushdeer.com/message/push' => Http::response([
            'code' => 0,
            'content' => [
                'result' => [
                    '{"counts":1,"logs":[],"success":"ok"}',
                    '{"counts":1,"logs":[],"success":"ok"}',
                ],
            ],
        ]),
    ]);
    PushDeer::messagePush(
        fake()->text(),
        fake()->text(),
        fake()->randomElement(['markdown', 'text', 'image'])
    );
    Http::assertSentCount(1);
    Http::assertSent(
        fn (Request $request, Response $response): bool => $request->isJson()
            && $request->hasHeader('X-Date-Time', now()->toDateTimeString('m'))
            && $response->ok()
    );

    Http::fake([
        '*://api2.pushdeer.com/message/no-content' => Http::response(status: 204),
    ]);
    PushDeer::get('message/no-content');
    Http::assertSentCount(1);
    Http::assertSent(
        fn (Request $request, Response $response): bool => $request->isJson()
            && $request->hasHeader('X-Date-Time', now()->toDateTimeString('m'))
            && $response->noContent()
    );
})->group(__DIR__, __FILE__);
