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

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * @see https://gist.github.com/bolechen/0c634a18d46a2aedbeb518a8176f8473
 *
 * @mixin \Illuminate\Database\Eloquent\Model
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
