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

namespace App\Models;

use App\Models\Concerns\Pipeable;
use App\Models\Concerns\SerializeDate;
use Awobaz\Compoships\Compoships;
use Envant\Fireable\FireableAttributes;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Macroable;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;
use Sushi\Sushi;

/**
 * @property \Spatie\SchemalessAttributes\SchemalessAttributes $extra_attributes
 */
class Model extends \Illuminate\Database\Eloquent\Model
{
    // use Compoships;
    // use HasUlids;
    // use HasUuids;
    // use MassPrunable;
    // use Prunable;
    // use QueryCacheable;
    // use Sushi;

    // use FireableAttributes;
    // use Macroable;
    use Pipeable;
    use SerializeDate;
    // use SoftDeletes;

    // protected static $unguarded = true;

    public $casts = [
        'extra_attributes' => SchemalessAttributes::class,
    ];

    #[Scope]
    public function withExtraAttributes(): Builder
    {
        return $this->extra_attributes->modelScope();
    }

    public function toDotArray(): array
    {
        return Arr::dot($this->toArray());
    }

    /**
     * @noinspection PhpRedundantMethodOverrideInspection
     */
    #[\Override]
    public function newCollection(array $models = []): Collection
    {
        return parent::newCollection($models);
    }

    /**
     * @noinspection PhpRedundantMethodOverrideInspection
     */
    #[\Override]
    public function getRouteKeyName(): string
    {
        return parent::getRouteKeyName();
    }

    /**
     * @noinspection PhpRedundantMethodOverrideInspection
     */
    #[\Override]
    public function is($model): bool
    {
        return parent::is($model);
    }

    /**
     * @noinspection PhpRedundantMethodOverrideInspection
     */
    #[\Override]
    public function isNot($model): bool
    {
        return parent::isNot($model);
    }
}
