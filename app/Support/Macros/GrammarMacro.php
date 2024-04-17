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

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Fluent;

/**
 * @mixin \Illuminate\Database\Schema\Grammars\Grammar
 */
class GrammarMacro
{
    /**
     * Compile a table comment command.
     */
    public function compileTableComment(): callable
    {
        /*
         * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
         * @param  \Illuminate\Support\Fluent  $command
         * @param  \Illuminate\Database\Connection  $connection
         * @return string
         */
        return fn (Blueprint $blueprint, Fluent $command, Connection $connection) => match ($connection->getDriverName()) {
            'mysql' => sprintf(
                'alter table %s comment = %s',
                $this->wrapTable($blueprint),
                "'".str_replace("'", "''", $command->comment)."'"
            ),
            'pgsql' => sprintf(
                'comment on table %s is %s',
                $this->wrapTable($blueprint),
                "'".str_replace("'", "''", $command->comment)."'"
            ),
            default => throw new \RuntimeException('The database driver in use does not support table comment.'),
        };
    }
}
