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

use App\Support\Inflector;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

class InflectorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inflector';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inflector pluralizes and singularizes English nouns.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        collect()
            ->pipe(function (Collection $collection) {
                while (true) {
                    $phrase = $this->ask('Please enter a phrase to inflect.');

                    if (filled($phrase)) {
                        break;
                    }
                }

                $type = $this->choice(
                    'Please choose the inflector type',
                    $types = [
                        'all',
                        'Laravel:plural',
                        'Laravel:singular',
                        'CakePHP:pluralize',
                        'CakePHP:singularize',
                        'CakePHP:camelize',
                        'CakePHP:variable',
                        'CakePHP:classify',
                        'CakePHP:tableize',
                        'CakePHP:underscore',
                        'CakePHP:dasherize',
                        'CakePHP:humanize',
                    ],
                    'all'
                );

                $classPluck = [
                    'Laravel' => Pluralizer::class,
                    'CakePHP' => Inflector::class,
                ];

                return collect('all' === $type ? \array_slice($types, 1) : [$type])
                    ->reduce(static function (Collection $results, string $type) use ($phrase, $classPluck) {
                        $result = Str::of($type)->explode(':')
                            ->pipe(static fn (Collection $parts) => $classPluck[$parts->first()]::{$parts->last()}($phrase));

                        return $results->add([
                            'type' => $type,
                            'result' => $result,
                        ]);
                    }, collect());
            })
            ->tap(function (Collection $results): void {
                $this->table(
                    ['Type', 'Result'],
                    $results->toArray()
                );
            });

        return self::SUCCESS;
    }
}
