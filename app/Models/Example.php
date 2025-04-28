<?php

/** @noinspection PhpMissingDocCommentInspection */
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

namespace App\Models;

use App\Models\Concerns\Pipeable;
use App\Models\Concerns\SerializeDate;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Traits\Macroable;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Sushi\Sushi;
use Watson\Validating\ValidatingTrait;

/**
 * @see https://github.com/LaravelDaily/laravel-tips
 * @see https://github.com/OussamaMater/Laravel-Tips
 */
final class Example extends BaseModel
{
    // use Compoships;
    // use HasFactory;
    // use HasUlids;
    // use HasUuids;
    // use Macroable;
    // use MassPrunable;
    // use Notifiable;
    // use Pipeable;
    // use Prunable;
    // use QueryCacheable;
    // use SerializeDate;
    // use SoftDeletes;
    // use Sushi;
    // use ValidatingTrait;
    use Notifiable;

    // protected static $unguarded = true;
    // protected $attributes = [];

    #[\Override]
    public function resolveRouteBinding($value, $field = null): Model
    {
        return parent::resolveRouteBinding($value, $field);
    }

    #[\Override]
    public function getRouteKeyName(): string
    {
        return parent::getRouteKeyName();
    }

    #[\Override]
    public static function query(): Builder
    {
        return parent::query();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\DatabaseNotification, $this>
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->latest();
    }

    #[\Override]
    public function newEloquentBuilder($query): Builder
    {
        return parent::newEloquentBuilder($query);
    }

    #[\Override]
    public function newCollection(array $models = []): Collection
    {
        return parent::newCollection($models);
    }

    #[\Override]
    public function is($model): bool
    {
        return parent::is($model);
    }

    #[\Override]
    public function isNot($model): bool
    {
        return parent::isNot($model);
    }

    #[\Override]
    protected function casts(): array
    {
        return parent::casts();
    }

    /**
     * @see https://github.com/LaravelDaily/laravel-tips/blob/master/db-models-and-eloquent.md#change-format-of-created_at-and-updated_at
     */
    private function createdAtFormatted(): Attribute
    {
        return Attribute::make(
            get: static fn (mixed $value, array $attributes) => $attributes['created_at']->format('Y-m-d H:i:s'),
        )->shouldCache();
    }

    /**
     * @see https://github.com/LaravelDaily/laravel-tips/blob/master/db-models-and-eloquent.md#change-format-of-created_at-and-updated_at
     */
    private function updatedAtFormatted(): Attribute
    {
        return Attribute::make(
            get: static fn (mixed $value, array $attributes) => $attributes['updated_at']->format('Y-m-d H:i:s'),
        )->withoutObjectCaching();
    }
}
