<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;
use Tests\Concerns\Bootloadable;

abstract class TestCase extends BaseTestCase
{
    // use Bootloadable;
    use CreatesApplication;
    // use DatabaseMigrations;
    // use DatabaseTruncation;
    // use FastRefreshDatabase;
    // use RefreshDatabase;

    /**
     * 在测试前指定要运行的 seeder
     *
     * @var string
     */
    // protected $seeder = OrderStatusSeeder::class;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        /** @see https://github.com/OussamaMater/Laravel-Tips#tip-167--time-travel-in-your-tests */
        // $this->travel(5)->years();
    }
}
