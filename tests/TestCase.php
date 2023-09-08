<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    // use DatabaseMigrations;
    // use DatabaseTruncation;
    use FastRefreshDatabase;
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
}
