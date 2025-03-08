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

namespace Tests\Feature;

class AuthTest extends TestCase
{
    protected $accessToken;

    #[\Override]
    protected function setUp(): void
    {
        self::markTestSkipped('Not implemented');
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
        self::assertSame('success', $content['status']);
        $response->assertOk();

        $this->accessToken = $content['data']['access_token'];
    }

    public function testMe(): void
    {
        $response = $this->withToken($this->accessToken)->get('/api/v1/auth/me');

        $content = json_decode($response->content(), true);
        self::assertSame('success', $content['status']);
        $response->assertOk();
    }

    public function testLogout(): void
    {
        $response = $this->withToken($this->accessToken)->post('/api/v1/auth/logout');

        $content = json_decode($response->content(), true);
        self::assertSame('success', $content['status']);
        $response->assertOk();
    }

    public function testRefresh(): void
    {
        $response = $this->withToken($this->accessToken)->post('/api/v1/auth/refresh');

        $content = json_decode($response->content(), true);
        self::assertSame('success', $content['status']);
        $response->assertOk();
    }
}
