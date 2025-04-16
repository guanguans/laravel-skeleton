<?php

/** @noinspection PhpUnusedAliasInspection */

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
    public function authorize(): bool
    {
        // return $this->user()->can('viewAny', JWTUser::class);

        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['integer', 'min:5', 'max:50'],
            'page' => ['integer', 'min:1'],
        ];
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
