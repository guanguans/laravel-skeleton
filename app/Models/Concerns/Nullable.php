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

/**
 * Nullable will set empty attributes to values equivalent to NULL in the database.
 *
 * @author Alexey Bobkov, Samuel Georges
 *
 * @see https://github.com/octobercms/library/blob/3.x/src/Database/Traits/Nullable.php
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait Nullable
{
    /**
     * initializeNullable trait for a model.
     */
    public function initializeNullable(): void
    {
        throw_unless(\is_array($this->nullable), \Exception::class, \sprintf(
            'The $nullable property in %s must be an array to use the Nullable trait.',
            static::class
        ));

        $this->bindEvent('model.beforeSave', function (): void {
            $this->nullableBeforeSave();
        });
    }

    /**
     * addNullable attribute to the nullable attributes list.
     */
    public function addNullable(null|array|string $attributes = null): void
    {
        $attributes = \is_array($attributes) ? $attributes : \func_get_args();

        $this->nullable = array_merge($this->nullable, $attributes);
    }

    /**
     * checkNullableValue checks if the supplied value is empty, excluding zero.
     *
     * @param string $value Value to check
     */
    public function checkNullableValue(string $value): bool
    {
        if (0 === $value || '0' === $value || 0.0 === $value || false === $value) {
            return false;
        }

        return empty($value);
    }

    /**
     * nullableBeforeSave will nullify empty fields at time of saving.
     */
    public function nullableBeforeSave(): void
    {
        foreach ($this->nullable as $field) {
            if ($this->checkNullableValue($this->{$field})) {
                if ($this->exists) {
                    $this->attributes[$field] = null;
                } else {
                    unset($this->attributes[$field]);
                }
            }
        }
    }
}
