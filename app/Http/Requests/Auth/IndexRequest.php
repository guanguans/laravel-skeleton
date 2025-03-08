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

use App\Models\JWTUser;
use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return optional($this->user())->can('viewAny', JWTUser::class);
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'per_page' => ['integer', 'min:5', 'max:50'],
            'page' => ['integer', 'min:1'],
        ];
    }

    #[\Override]
    public function messages()
    {
        return [
            'per_page.integer' => ':attribute :input 不可用。',
        ];
    }
}
