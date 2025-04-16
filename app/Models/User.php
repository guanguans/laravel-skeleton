<?php

/** @noinspection ClassOverridesFieldOfSuperClassInspection */

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

use App\Models\Concerns\SerializeDate;
use App\Observers\UserObserver;
use Database\Factories\UserFactory;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[ObservedBy(UserObserver::class)]
#[UseFactory(UserFactory::class)]
class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use Notifiable;
    use SerializeDate;

    /** The model's default values for attributes. */
    protected $attributes = [
        // 'options' => '[]',
        // 'delayed' => false,
    ];

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function newEloquentBuilder($query): Builder
    {
        return new Builder($query);
    }

    #[\Override]
    public static function query(): Builder
    {
        return parent::query();
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param mixed $value
     * @param null|string $field
     *
     * @return null|\Illuminate\Database\Eloquent\Model
     */
    #[\Override]
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)->firstOrFail();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\App\Models\DatabaseNotification, $this>
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->latest();
    }

    /**
     * @todo implement
     */
    public function isAdmin(): bool
    {
        return false;
    }

    /**
     * @todo implement
     */
    public function isDeveloper(): bool
    {
        return false;
    }

    /**
     * @todo implement
     */
    public function locale(): string
    {
        return $this->locale ?? app()->currentLocale();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function createdAtFormatted(): Attribute
    {
        return Attribute::make(
            get: static fn ($value, $attributes) => $attributes['created_at']->format('Y-m-d H:i:s'),
        )->shouldCache();
    }

    protected function updatedAtFormatted(): Attribute
    {
        return Attribute::make(
            get: static fn ($value, $attributes) => $attributes['updated_at']->format('Y-m-d H:i:s'),
        )->withoutObjectCaching();
    }
}
