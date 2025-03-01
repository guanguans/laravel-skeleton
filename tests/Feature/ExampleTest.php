<?php

namespace Tests\Feature;

class ExampleTest extends TestCase
{
    #[\Override]
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
