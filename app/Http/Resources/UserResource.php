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

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\JWTUser
 * @mixin \App\Models\User
 */
class UserResource extends JsonResource
{
    #[\Override]
    public function toArray(Request $request): array|Arrayable|\JsonSerializable
    {
        return parent::toArray($request);
    }
}
