<?php

declare(strict_types=1);

namespace App\Macros;

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
        return function (Blueprint $blueprint, Fluent $command, Connection $connection) {
            switch ($connection->getDriverName()) {
                case 'mysql':
                    return sprintf(
                        'alter table %s comment = %s',
                        $this->wrapTable($blueprint),
                        "'".str_replace("'", "''", $command->comment)."'"
                    );

                case 'pgsql':
                    return sprintf(
                        'comment on table %s is %s',
                        $this->wrapTable($blueprint),
                        "'".str_replace("'", "''", $command->comment)."'"
                    );

                case 'sqlsrv':
                case 'sqlite':
                default:
                    throw new \RuntimeException('The database driver in use does not support table comment.');
            }
        };
    }
}
