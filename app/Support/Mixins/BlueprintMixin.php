<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Mixins;

use App\Support\Attributes\Mixin;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;

/**
 * @mixin \Illuminate\Database\Schema\Blueprint
 */
#[Mixin(Blueprint::class)]
class BlueprintMixin
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
            $default = null
        ): ColumnDefinition {
            $columnDefinition = $this->decimal($column, $total, $places);

            if ($unsigned) {
                $columnDefinition->unsigned();
            }

            if ($default !== null) {
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
        /*
         * @param  string  $comment
         * @return \Illuminate\Support\Fluent
         */
        return fn ($comment) => $this->addCommand('tableComment', ['comment' => $comment]);
    }

    public function hasIndex(): \Closure
    {
        return function (string $index): bool {
            $schemaManager = Schema::getConnection()->getDoctrineSchemaManager();

            return $schemaManager->listTableDetails($this->getTable())->hasIndex($index);
        };
    }

    public function dropIndexIfExists(): \Closure
    {
        return function (string $index): Fluent {
            /** @var \Illuminate\Database\Schema\Blueprint $this */
            if ($this->hasIndex($index)) {
                return $this->dropIndex($index);
            }

            return new Fluent;
        };
    }
}
