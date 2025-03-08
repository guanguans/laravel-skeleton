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

namespace App\Console\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\suggest;
use function Laravel\Prompts\text;

/**
 * @see https://github.com/binafy/artisan-finder/blob/1.x/src/Commands/FindCommand.php
 */
class FindCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find:art {args?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find artisan command with given name';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $commands = collect(array_keys($this->getApplication()->all()))
            ->filter(fn (string $command): bool => $command !== $this->signature)
            ->values();

        $command = suggest(
            'Search for a command',
            options: $commands->toArray(),
            required: true,
            hint: 'Type parts of a command name to search for'
        );

        $args = [];

        if ($this->argument('args')) {
            $args = text(
                label: 'Write arguments:',
                placeholder: 'E.g. Milwad',
                hint: 'This will be sent as command arguments'
            );
            $args = explode(' ', $args);
        }

        return $this->call($command, $args);
    }
}
