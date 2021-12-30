<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthTest extends TestCase
{
    protected $accessToken;

    public function setUp(): void
    {
        parent::setUp();

        $response = $this->post('/api/v1/auth/register', [
            'email' => 'example@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $response->assertOk();

        $response = $this->post('/api/v1/auth/login', [
            'email' => 'example@example.com',
            'password' => '12345678',
        ]);

        $content = json_decode($response->content(), true);
        $this->assertEquals('success', $content['status']);
        $response->assertOk();

        $this->accessToken = $content['data']['access_token'];
    }

    public function test_me()
    {
        $response = $this->withToken($this->accessToken)->get('/api/v1/auth/me');

        $content = json_decode($response->content(), true);
        $this->assertEquals('success', $content['status']);
        $response->assertOk();
    }

    public function test_logout()
    {
        $response = $this->withToken($this->accessToken)->post('/api/v1/auth/logout');

        $content = json_decode($response->content(), true);
        $this->assertEquals('success', $content['status']);
        $response->assertOk();
    }

    public function test_refresh()
    {
        $response = $this->withToken($this->accessToken)->post('/api/v1/auth/refresh');

        $content = json_decode($response->content(), true);
        $this->assertEquals('success', $content['status']);
        $response->assertOk();
    }
}
