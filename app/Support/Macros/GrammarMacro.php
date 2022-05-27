<?php

namespace App\Support\Macros;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Fluent;
use RuntimeException;

class GrammarMacro
{
    /**
     * Compile a table comment command.
     *
     * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
     * @param  \Illuminate\Support\Fluent  $command
     * @return string
     */
    public function compileTableComment(): callable
    {
        return function (Blueprint $blueprint, Fluent $command, Connection $connection) {
            /* @var \Illuminate\Database\Schema\Grammars\Grammar $this */
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
                    throw new RuntimeException('The database driver in use does not support table comment.');
            }
        };
    }
}
