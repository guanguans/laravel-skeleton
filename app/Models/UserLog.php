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

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class UserLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'country_name',
        'country_code',
    ];

    /* -------------------------------------------------------------------------------------------- */
    // Accessors & Mutators
    /* -------------------------------------------------------------------------------------------- */
    public function date(): Attribute
    {
        return new Attribute(
            get: static fn (mixed $value, array $attributes) => Carbon::parse($attributes['created_at'])->inUserTimezone()->isoFormat('dddd LL'),
        );
    }

    public function time(): Attribute
    {
        return new Attribute(
            get: static fn (mixed $value, array $attributes) => Carbon::parse($attributes['created_at'])->inUserTimezone()->format('H:i:s'),
        );
    }

    /* -------------------------------------------------------------------------------------------- */
    // Relationships
    /* -------------------------------------------------------------------------------------------- */
    public function user(): BelongsTo
    {
        // return $this->hasMany(User::class)->inverse()->chaperone('post');

        return $this->belongsTo(User::class)->withTrashed();
    }
}
