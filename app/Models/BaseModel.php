<?php

/** @noinspection LaravelUnknownEloquentFactoryInspection */

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

use App\Models\Concerns\HasSchemalessAttributes;
use App\Models\Concerns\SerializeDate;
use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Watson\Validating\ValidatingTrait;

/**
 * @see https://github.com/LaravelDaily/laravel-tips
 * @see https://github.com/OussamaMater/Laravel-Tips
 * @see https://github.com/kirkbushell/eloquence
 */
class BaseModel extends Model
{
    use HasCamelCasing;
    use HasFactory;
    use HasSchemalessAttributes;
    use SerializeDate;
    use SoftDeletes;
    use ValidatingTrait;

    public static function getTableName(): \Closure
    {
        return static fn (): string => (new static)->getTable();
    }

    public function toDotArray(): array
    {
        return collect($this->toArray())->dot()->all();
    }
}
