<?php

/** @noinspection LaravelUnknownEloquentFactoryInspection */

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

use App\Models\Concerns\HasSchemalessAttributes;
use App\Models\Concerns\SerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @see https://github.com/kirkbushell/eloquence
 * @see https://github.com/LaravelDaily/laravel-tips
 * @see https://github.com/OussamaMater/Laravel-Tips
 */
class Model extends EloquentModel
{
    // use Eloquence\Behaviours\HasCamelCasing;
    // use Watson\Validating\ValidatingTrait;
    use HasFactory;
    use HasSchemalessAttributes;
    use SerializeDate;
    use SoftDeletes;

    /**
     * @see self::__callStatic()
     */
    public static function getTableName(): string
    {
        return (new static)->getTable();
    }

    public function toDotArray(): array
    {
        return collect($this->toArray())->dot()->all();
    }
}
