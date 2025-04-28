<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection DebugFunctionUsageInspection */
/** @noinspection PhpInternalEntityUsedInspection */
/** @noinspection PhpUndefinedClassInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace Tests\Concerns;

use Illuminate\Support\Str;
use Pest\TestSuite;

/**
 * @see https://github.com/capsulescodes/articles/blob/019-access-laravel-before-and-after-running-pest-tests/tests/Traits/Bootloadable.php
 * @see https://capsules.codes/en/blog/fyi/en-fyi-access-laravel-before-and-after-running-pest-tests
 *
 * @mixin \Illuminate\Foundation\Testing\TestCase
 */
trait Bootloadable
{
    private static int $count = 0;
    private static array $tests;
    private static string $current;

    protected function setUp(): void
    {
        parent::setUp();

        self::$current = array_reverse(explode('\\', debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class']))[0];

        if (!isset(self::$tests)) {
            $this->init();
        }

        if (!self::$count && method_exists(self::class, 'initialize')) {
            $this->initialize(self::$current);
        }

        ++self::$count;
    }

    protected function tearDown(): void
    {
        if (self::$tests[self::$current] === self::$count) {
            if (method_exists(self::class, 'finalize')) {
                $this->finalize(self::$current);
            }

            self::$count = 0;
        }

        parent::tearDown();
    }

    private function init(): void
    {
        $repository = TestSuite::getInstance()->tests;

        $data = [];

        foreach ($repository->getFilenames() as $file) {
            $factory = $repository->get($file);

            $filename = Str::of($file)->basename()->explode('.')->first();

            if (self::class === $factory->class) {
                $data = [...$data, ...[$filename => \count($factory->methods)]];
            }
        }

        self::$tests = $data;
    }
}
