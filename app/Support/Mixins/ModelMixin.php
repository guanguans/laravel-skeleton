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

namespace App\Support\Mixins;

use App\Models\Model;
use App\Support\Attributes\Mixin;

/**
 * @mixin \App\Models\Model
 */
#[Mixin(Model::class)]
final class ModelMixin
{
    public static function getTableName(): \Closure
    {
        return static fn (): string => (new self)->getTable();
    }
}
