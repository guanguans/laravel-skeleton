<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Console\Commands;

use Illuminate\Support\Collection;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Traits\ForwardsCalls;
use Symfony\Component\String\Inflector\EnglishInflector;

final class InflectorCommand extends Command
{
    use ForwardsCalls;

    /** @noinspection ClassOverridesFieldOfSuperClassInspection */
    #[\Override]
    protected $signature = 'inflector {phrase? : The word or phrase to be inflected}';

    /** @noinspection ClassOverridesFieldOfSuperClassInspection */
    #[\Override]
    protected $description = 'Inflector pluralizes and singularizes English nouns.';

    public function handle(): void
    {
        collect()
            ->tap(function () use (&$phrase): void {
                while (blank($phrase = $this->argument('phrase'))) {
                    if (filled($phrase = $this->ask('Please enter a phrase to inflect.'))) {
                        break;
                    }
                }
            })
            ->pipe(
                static fn (): Collection => collect([
                    'Laravel:plural' => Pluralizer::plural(...),
                    'Laravel:singular' => Pluralizer::singular(...),
                    'Symfony:pluralize' => (new EnglishInflector)->pluralize(...),
                    'Symfony:singularize' => (new EnglishInflector)->singularize(...),
                ])->map(static fn (callable $callback, string $type): array => [
                    'type' => $type,
                    'result' => collect($callback($phrase))->implode('、'),
                ])
            )
            ->tap(fn (Collection $results) => $this->table(['Type', 'Result'], $results));
    }

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function rules(): array
    {
        return [
            'phrase' => 'nullable|filled',
        ];
    }
}
