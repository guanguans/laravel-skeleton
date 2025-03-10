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
use Illuminate\Support\Str;

/**
 * @see https://github.com/hashemirafsan/fireworks
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait Fireworks
{
    protected static function boot(): void
    {
        $self = new self;

        // before event list
        $beforeEvents = ['creating', 'updating', 'saving', 'deleting'];
        // after event list
        $afterEvents = ['retrieved', 'created', 'updated', 'saved', 'deleted'];

        foreach ($beforeEvents as $event) {
            static::{$event}(static function (Model $model) use ($self, $event): void {
                $method = \sprintf('onModel%s', Str::studly($event));
                $self->callBeforeEvent($model, $event);

                if (method_exists($model, $method)) {
                    \call_user_func_array([$model, $method], [$model]);
                }
            });
        }

        foreach ($afterEvents as $event) {
            static::{$event}(static function (Model $model) use ($self, $event): void {
                $method = \sprintf('onModel%s', Str::studly($event));

                if (method_exists($model, $method)) {
                    \call_user_func_array([$model, $method], [$model]);
                }

                $self->callAfterEvent($model, $event);
            });
        }
    }

    private function callBeforeEvent(Model $model, string $event): void
    {
        $this->callColumnsEvent($model, 'onModel%s'.Str::studly($event));
    }

    private function callAfterEvent(Model $model, string $event): void
    {
        $this->callColumnsEvent($model, 'onModel%s'.Str::studly($event));
    }

    private function callColumnsEvent(Model $model, string $methodConvention): void
    {
        foreach ($model->getDirty() ?? [] as $column => $newValue) {
            $method = \sprintf($methodConvention, Str::studly($column));

            if (method_exists($model, $method)) {
                \call_user_func_array([$model, $method], [$model, $model->getOriginal($column), $newValue]);
            }
        }
    }
}
