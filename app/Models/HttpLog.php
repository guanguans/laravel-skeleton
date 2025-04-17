<?php

/** @noinspection ClassOverridesFieldOfSuperClassInspection */
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

use App\Models\Concerns\SerializeDate;
use Illuminate\Database\Eloquent\SoftDeletes;

class HttpLog extends Model
{
    use SerializeDate;
    // use SoftDeletes;

    protected $table = 'http_log';
    protected $guarded = [];
}
