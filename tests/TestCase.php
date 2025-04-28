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

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use JMac\Testing\Traits\AdditionalAssertions;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Tests\Concerns\Bootloadable;

abstract class TestCase extends BaseTestCase
{
    use AdditionalAssertions;
    // use Bootloadable;
    // use DatabaseMigrations;
    // use DatabaseTruncation;
    // use FastRefreshDatabase;
    // use LazilyRefreshDatabase;
    // use RefreshDatabase;

    /**
     * 在测试前指定要运行的 seeder.
     *
     * @var string
     */
    // protected $seeder = OrderStatusSeeder::class;

    /**
     * Indicates whether the default seeder should run before each test.
     */
    protected bool $seed = false;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        /** @see https://github.com/OussamaMater/Laravel-Tips#tip-167--time-travel-in-your-tests */
        // $this->travel(5)->years();
    }
}
