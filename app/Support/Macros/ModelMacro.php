<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Macros;

/**
 * @mixin \App\Models\Model
 */
class ModelMacro
{
    public static function getTableName(): \Closure
    {
        return static fn (): string => (new static())->getTable();
    }
}
