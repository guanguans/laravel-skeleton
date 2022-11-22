<?php

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
            ->whenRouteIs('auth.login', function (self $request, $value) use (&$rules) {
                $rules = [
                    'email' => 'required|email',
                    'password' => 'required|string',
                ];
            })
            ->whenRouteIs('auth.register', function (self $request) use (&$rules) {
                $rules = [
                    'email' => 'required|email|unique:App\Models\JWTUser,email',
                    'password' => 'required|string|min:8|confirmed',
                    'password_confirmation' => 'required|same:password',
                ];
            })
            ->whenRouteIs('auth.index', function () use (&$rules) {
                $rules = [
                    'per_page' => 'integer|min:5|max:50',
                    'page' => 'integer|min:1',
                ];
            });

        return (array) $rules;
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages()
    {
        return [
            'per_page.integer' => ':attribute :input 不可用。',
        ];
    }
}
