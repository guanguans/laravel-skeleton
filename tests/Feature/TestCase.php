<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

class TestCase extends \Tests\TestCase
{
    // use DatabaseMigrations;
    // use DatabaseTruncation;
    // use FastRefreshDatabase;
    use RefreshDatabase;
}
