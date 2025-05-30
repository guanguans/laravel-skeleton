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

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;

/**
 * @see https://github.com/laravel/framework/commit/9ccf0031d1cb8669752bc95e85cdccad20706461
 * @see https://github.com/laravel/framework/blob/10.x/src/Illuminate/Database/Connection.php
 *
 * @mixin \Illuminate\Database\Schema\Blueprint
 */
#[Mixin(Blueprint::class)]
final class BlueprintMixin
{
    /**
     * @see https://xammar-aldwayma.me/blog/avoid-duplication-in-laravel-migrations-with-custom-blueprint-macros
     */
    public function money(): \Closure
    {
        return function (
            string $column,
            int $total = 16,
            int $places = 2,
            bool $unsigned = false,
            ?float $default = null
        ): ColumnDefinition {
            $columnDefinition = $this->decimal($column, $total, $places);

            if ($unsigned) {
                $columnDefinition->unsigned();
            }

            if (null !== $default) {
                $columnDefinition->default($default);
            }

            return $columnDefinition;
        };
    }

    /**
     * Add a comment to the table.
     *
     * ```php
     * \Illuminate\Support\Facades\Schema::table('users', function (Blueprint $table) {
     *    $table->comment('用户表');
     * });
     * ```
     */
    public function comment(): \Closure
    {
        /**
         * @return \Illuminate\Support\Fluent
         */
        return fn (string $comment) => $this->addCommand('tableComment', ['comment' => $comment]);
    }
}
