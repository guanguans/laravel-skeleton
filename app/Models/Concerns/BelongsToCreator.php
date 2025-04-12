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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id')->withTrashed();
    }

    public function isCreatedBy(User $user): bool
    {
        return $this->creator_id === $user->id;
    }
}
