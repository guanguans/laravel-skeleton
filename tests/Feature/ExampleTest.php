<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    protected function setUp(): void
    {
        $this->markTestSkipped('Not implemented');
        parent::setUp();
    }

    /**
     * A basic test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }
}
