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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Orbit\Concerns\Orbital;
use Orbit\Concerns\SoftDeletes;

final class Movie extends Model
{
    use Orbital;
    use SerializeDate;
    use SoftDeletes;
    public static string $driver = 'json';
    protected $fillable = [
        'name',
        'director',
    ];

    public static function schema(Blueprint $table): void
    {
        $table->string('name');
        $table->string('director');
        // $table->timestamps();
        $table->softDeletes();
    }

    public static function enableOrbit(): bool
    {
        return true;
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function getKeyName(): string
    {
        return 'name';
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    public function getIncrementing(): bool
    {
        return false;
    }
}
