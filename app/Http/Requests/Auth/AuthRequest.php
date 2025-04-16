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

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public function rules(): array
    {
        $this->is('api/v1/auth/login');

        return match (true) {
            $this->routeIs('auth.register') => [
                'email' => 'required|email|unique:App\Models\JWTUser,email',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|same:password',
            ],
            $this->routeIs('auth.login') => [
                'email' => 'required|email',
                'password' => 'required|string',
            ],
            $this->routeIs('auth.index') => [
                'per_page' => 'integer|min:5|max:50',
                'page' => 'integer|min:1',
            ],
            default => [],
        };
    }

    public function authorize(): bool
    {
        return match (true) {
            $this->routeIs('auth.logout'), $this->routeIs('auth.update') => $this->user()->can('update'),
            default => true,
        };
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function messages(): array
    {
        return [
            'per_page.integer' => ':attribute :input 不可用。',
        ];
    }
}
