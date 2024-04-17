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

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property \App\Models\User $creator
 * @property int $creator_id
 *
 * @method static creating(\Closure $closure)
 * @method static updating(\Closure $closure)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait BelongsToCreator
{
    public static function bootBelongsToCreator(): void
    {
        static::creating(
            static function (Model $model): void {
                $model->creator_id ??= auth()->id();
            }
        );

        static::updating(
            static function (Model $model): void {
                abort_if($model->isDirty('creator_id'), 403, '创建者不可更新');
            }
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id')->withTrashed();
    }

    public function isCreatedBy(User $user): bool
    {
        return $this->creator_id === $user->id;
    }
}
