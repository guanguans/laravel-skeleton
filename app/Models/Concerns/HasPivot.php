<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * @see https://gist.github.com/bolechen/0c634a18d46a2aedbeb518a8176f8473
 */
trait HasPivot
{
    /**
     * 检查多对多关系是否存在.
     */
    public function hasPivot(string $relation, Model $model): bool
    {
        return (bool) $this->{$relation}()
            ->wherePivot($model->getForeignKey(), $model->{$model->getKeyName()})
            ->count();
    }
}
