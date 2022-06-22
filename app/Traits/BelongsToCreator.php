<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \App\Models\User $creator
 * @property string           $creator_id
 * @method static creating(\Closure $param)
 * @method static updating(\Closure $param)
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

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id')->withTrashed();
    }

    public function isCreatedBy(User $user): bool
    {
        if ($user instanceof User) {
            $user = $user->id;
        }

        return $this->creator_id == (int)$user;
    }
}
