<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpVoidFunctionResultUsedInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpAttributeCanBeAddedToOverriddenMemberInspection */
/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithCachedConfig;
use Illuminate\Foundation\Testing\WithCachedRoutes;
use JMac\Testing\Traits\AdditionalAssertions;

abstract class TestCase extends BaseTestCase
{
    // use AdditionalAssertions;
    // use DatabaseMigrations;
    // use DatabaseTruncation;
    // use FastRefreshDatabase;
    // use LazilyRefreshDatabase;
    // use RefreshDatabase;
    // use WithCachedConfig;
    // use WithCachedRoutes;
    use AdditionalAssertions;
    use RefreshDatabase;

    // /** 在测试前指定要运行的 seeder. */
    // protected string $seeder = OrderStatusSeeder::class;

    /** Indicates whether the default seeder should run before each test. */
    protected bool $seed = false;
}
