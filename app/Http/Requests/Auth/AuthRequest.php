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
use Illuminate\Support\Traits\Conditionable;

class AuthRequest extends FormRequest
{
    use Conditionable;

    public function rules(): array
    {
        // // $request->is('api/v1/auth/login');
        // $this
        //     ->when($this->routeIs('auth.login'), function (self $request) use (&$rules) {
        //         $rules = [
        //             'email' => 'required|email',
        //             'password' => 'required|string',
        //         ];
        //     })
        //     ->when($this->routeIs('auth.register'), function (self $request) use (&$rules) {
        //         $rules = [
        //             'email' => 'required|email|unique:App\Models\JWTUser,email',
        //             'password' => 'required|string|min:8|confirmed',
        //             'password_confirmation' => 'required|same:password',
        //         ];
        //     })
        //     ->when($this->routeIs('auth.index'), function (self $request) use (&$rules) {
        //         $rules = [
        //             'per_page' => 'integer|min:5|max:50',
        //             'page' => 'integer|min:1'
        //         ];
        //     });

        $this
            ->whenRouteIs('auth.login', static function (self $request, $value) use (&$rules): void {
                $rules = [
                    'email' => 'required|email',
                    'password' => 'required|string',
                ];
            })
            ->whenRouteIs('auth.register', static function (self $request) use (&$rules): void {
                $rules = [
                    'email' => 'required|email|unique:App\Models\JWTUser,email',
                    'password' => 'required|string|min:8|confirmed',
                    'password_confirmation' => 'required|same:password',
                ];
            })
            ->whenRouteIs('auth.index', static function () use (&$rules): void {
                $rules = [
                    'per_page' => 'integer|min:2|max:50',
                    'page' => 'integer|min:1',
                ];
            });

        return (array) $rules;
    }

    public function authorize(): bool
    {
        return true;
    }

    #[\Override]
    public function messages()
    {
        return [
            'per_page.integer' => ':attribute :input 不可用。',
        ];
    }
}
