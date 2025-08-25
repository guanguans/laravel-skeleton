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

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

/**
 * @see https://github.com/spatie/laravel-schemaless-attributes
 *
 * @property \Spatie\SchemalessAttributes\SchemalessAttributes $extra_attributes
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasSchemalessAttributes
{
    public function initializeHasSchemalessAttributes(): void
    {
        $this->casts['extra_attributes'] = SchemalessAttributes::class;
    }

    // public function scopeWithExtraAttributes(): Builder
    // {
    //     return $this->extra_attributes->modelScope();
    // }

    #[Scope]
    protected function withExtraAttributes(): Builder
    {
        return $this->extra_attributes->modelScope();
    }
}
