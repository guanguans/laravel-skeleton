<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Plannr\Laravel\FastRefreshDatabase\Traits\FastRefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    // use RefreshDatabase;
    use FastRefreshDatabase;
}
