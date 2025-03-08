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
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Support\Fluent;

/**
 * @mixin \Illuminate\Database\Schema\Grammars\Grammar
 */
#[Mixin(Grammar::class)]
class GrammarMixin
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
        return fn (Blueprint $blueprint, Fluent $command, Connection $connection): string => match ($connection->getDriverName()) {
            'mysql' => \sprintf(
                'alter table %s comment = %s',
                $this->wrapTable($blueprint),
                "'".str_replace("'", "''", $command->comment)."'"
            ),
            'pgsql' => \sprintf(
                'comment on table %s is %s',
                $this->wrapTable($blueprint),
                "'".str_replace("'", "''", $command->comment)."'"
            ),
            default => throw new \RuntimeException('The database driver in use does not support table comment.'),
        };
    }
}
