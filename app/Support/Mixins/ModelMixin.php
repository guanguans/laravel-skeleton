<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Mixins;

use App\Models\Model;
use App\Support\Attributes\Mixin;

/**
 * @mixin \App\Models\Model
 */
#[Mixin(Model::class)]
class ModelMixin
{
    public static function getTableName(): \Closure
    {
        return static fn (): string => (new static)->getTable();
    }
}
