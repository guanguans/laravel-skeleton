<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Macros;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;

/**
 * @mixin \Illuminate\Database\Schema\Blueprint
 */
class BlueprintMacro
{
    /**
     * Add a comment to the table.
     *
     * ```php
     * \Illuminate\Support\Facades\Schema::table('users', function (Blueprint $table) {
     *    $table->comment('用户表');
     * });
     * ```
     */
    public function comment(): callable
    {
        /*
         * @param  string  $comment
         * @return \Illuminate\Support\Fluent
         */
        return fn ($comment) => $this->addCommand('tableComment', ['comment' => $comment]);
    }

    public function hasIndex(): callable
    {
        return function (string $index): bool {
            $schemaManager = Schema::getConnection()->getDoctrineSchemaManager();

            return $schemaManager->listTableDetails($this->getTable())->hasIndex($index);
        };
    }

    public function dropIndexIfExists(): callable
    {
        return function (string $index): Fluent {
            /** @var \Illuminate\Database\Schema\Blueprint $this */
            if ($this->hasIndex($index)) {
                return $this->dropIndex($index);
            }

            return new Fluent();
        };
    }
}
