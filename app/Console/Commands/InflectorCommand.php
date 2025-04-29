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

use Illuminate\Support\Collection;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;
use Symfony\Component\String\Inflector\EnglishInflector;

final class InflectorCommand extends Command
{
    protected $signature = 'inflector {phrase? : The word or phrase to be inflected}';
    protected $description = 'Inflector pluralizes and singularizes English nouns.';

    public function handle(): void
    {
        collect()
            ->pipe(function () {
                while (blank($phrase = $this->argument('phrase'))) {
                    if (filled($phrase = $this->ask('Please enter a phrase to inflect.'))) {
                        break;
                    }
                }

                $type = $this->choice(
                    'Please choose the inflector type',
                    $types = [
                        'all',
                        'Laravel:plural',
                        'Laravel:singular',
                        'Symfony:pluralize',
                        'Symfony:singularize',
                    ],
                    'all'
                );

                return collect('all' === $type ? \array_slice($types, 1) : [$type])
                    ->reduce(
                        static fn (Collection $results, string $type) => $results->add([
                            'type' => $type,
                            'result' => Str::of($type)
                                ->explode(':', 2)
                                ->pipe(
                                    static fn (Collection $parts): mixed => \is_array($result = \call_user_func(
                                        [
                                            app(
                                                [
                                                    'Laravel' => Pluralizer::class,
                                                    'Symfony' => EnglishInflector::class,
                                                ][$parts->first()]
                                            ),
                                            $parts->last(),
                                        ],
                                        $phrase
                                    ))
                                        ? implode('、', $result)
                                        : $result
                                ),
                        ]),
                        collect()
                    );
            })
            ->tap(function (Collection $results): void {
                $this->table(['Type', 'Result'], $results->toArray());
            });
    }

    #[\Override]
    protected function rules(): array
    {
        return [
            'phrase' => 'nullable|filled',
        ];
    }
}
