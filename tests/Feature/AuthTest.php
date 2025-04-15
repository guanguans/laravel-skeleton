<?php

/** @noinspection AnonymousFunctionStaticInspection */
/** @noinspection NullPointerExceptionInspection */
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->response = $this
        ->postJson('/api/v1/auth/register', $this->credentials = [
            'email' => 'example@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ])
        // ->ddJson()
        // ->ddBody()
        ->assertOk()
        ->assertJsonStructure($this->structure = [
            'status',
            'code',
            'message',
            'data' => [
                'access_token',
                'token_type',
                'expires_in',
            ],
            'error',
        ]);

    $this->accessToken = $this->response->json('data.access_token');
});

it('can login', function (): void {
    $this
        ->postJson('/api/v1/auth/login', $this->credentials)
        // ->ddJson()
        // ->ddBody()
        ->assertOk()
        ->assertJsonStructure($this->structure);
})->group(__DIR__, __FILE__);

it('can get me', function (): void {
    $this
        ->withToken($this->accessToken)
        ->getJson('/api/v1/auth/me')
        // ->ddJson()
        // ->ddBody()
        ->assertOk()
        ->assertJsonStructure(
            [
                'data' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
            ] + $this->structure,
        );
})->group(__DIR__, __FILE__);

it('can logout', function (): void {
    $this
        ->withToken($this->accessToken)
        ->postJson('/api/v1/auth/logout')
        // ->ddJson()
        // ->ddBody()
        ->assertOk()
        ->assertJsonStructure([
            'status',
            'code',
            'message',
            'data',
            'error',
        ]);
})->group(__DIR__, __FILE__);

it('can refresh', function (): void {
    $this
        ->withToken($this->accessToken)
        ->postJson('/api/v1/auth/refresh')
        // ->ddJson()
        // ->ddBody()
        ->assertOk()
        ->assertJsonStructure($this->structure);
})->group(__DIR__, __FILE__);

it('can get index', function (): void {
    $this
        ->getJson('/api/v1/auth/index')
        // ->ddJson()
        // ->ddBody()
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'data' => [
                    [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'path',
                    'per_page',
                    'to',
                ],
            ],
        ] + $this->structure);
})->group(__DIR__, __FILE__);
