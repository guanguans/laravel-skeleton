<?php

/** @noinspection ClassOverridesFieldOfSuperClassInspection */
/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
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
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[ObservedBy(UserObserver::class)]
#[UseFactory(UserFactory::class)]
class User extends Authenticatable implements MustVerifyEmail
{
    // use Parental\HasChildren;

    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use Notifiable;
    use SerializeDate;

    /** @var list<string> */
    #[\Override]
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /** @var list<string> */
    #[\Override]
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @var array<string, mixed> */
    protected array $rules = [
        'email' => 'required|email',
        'password' => 'required|string',
    ];

    public function isAdmin(): bool
    {
        // return (bool) $this->is_admin;
        throw new \LogicException('Not implemented yet.');
    }

    public function isDeveloper(): bool
    {
        // return str($this->name)->is(config('services.developer.names'))
        //     && str($this->email)->is(config('services.developer.emails'));
        throw new \LogicException('Not implemented yet.');
    }

    public function locale(): string
    {
        // return $this->locale ?? app()->currentLocale();
        throw new \LogicException('Not implemented yet.');
    }

    public function timezone(): string
    {
        // return $this->timezone ?? config('app.timezone');
        throw new \LogicException('Not implemented yet.');
    }

    #[Scope]
    protected function verifiedEmail(Builder $query): void
    {
        $query->whereNotNull('email_verified_at');
    }

    /**
     * @return array<string, string>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
