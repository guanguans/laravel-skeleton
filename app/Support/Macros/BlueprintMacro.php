<?php

namespace App\Support\Macros;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;

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
     *
     * @param  string  $comment
     * @return \Illuminate\Support\Fluent
     */
    public function comment(): callable
    {
        return function ($comment) {
            /* @var \Illuminate\Database\Schema\Blueprint $this */
            return $this->addCommand('tableComment', compact('comment'));
        };
    }

    public function hasIndex(): callable
    {
        return  function (string $index): bool {
            $schemaManager = Schema::getConnection()->getDoctrineSchemaManager();

            /** @var \Illuminate\Database\Schema\Blueprint $this */
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
