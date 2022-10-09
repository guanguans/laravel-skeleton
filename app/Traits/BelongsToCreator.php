<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property \App\Models\User $creator
 * @property int           $creator_id
 * @method static creating(\Closure $closure)
 * @method static updating(\Closure $closure)
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait BelongsToCreator
{
    public static function bootBelongsToCreator()
    {
        static::creating(
            function (Model $model) {
                $model->creator_id = $model->creator_id ?? \auth()->id();
            }
        );

        static::updating(
            function (Model $model) {
                \abort_if($model->isDirty('creator_id'), 403, '创建者不可更新');
            }
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id')->withTrashed();
    }

    public function isCreatedBy(User $user): bool
    {
        return $this->creator_id == $user->id;
    }
}
