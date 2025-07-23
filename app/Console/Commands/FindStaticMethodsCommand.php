<?php

/** @noinspection PhpUnusedAliasInspection */

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

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\EnumeratesValues;
use Illuminate\Support\Traits\Macroable;
use PHPUnit\Framework\Assert;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use function Laravel\Prompts\select;

final class FindStaticMethodsCommand extends Command
{
    protected $signature = 'find:static-methods';
    protected $description = 'Find static methods';

    /**
     * @noinspection PhpMemberCanBePulledUpInspection
     *
     * @throws \ReflectionException
     */
    public function handle(): void
    {
        $this->findStaticMethods();
    }

    #[\Override]
    protected function rules(): array
    {
        return [];
    }

    /**
     * @throws \ReflectionException
     */
    private function findStaticMethods(): void
    {
        classes(
            static fn (string $class): bool => str($class)->is([
                'Illuminate\\*',
            ]) && !str($class)->is([
                // Arr::class,
                // Number::class,
                // Str::class,
                Carbon::class,
            ]) && !collect([
                // Model::class,
                // Connection::class,
                // Relation::class,
                Builder::class,
            ])->first(static fn (string $exceptClass): bool => is_subclass_of($class, $exceptClass))
        )
            ->reject(
                static fn (\ReflectionClass $reflectionClass): bool => $reflectionClass->isInterface()
                    || ($reflectionClass->isInstantiable() && !$reflectionClass->getConstructor())
            )
            ->map(
                static fn (
                    \ReflectionClass $reflectionClass
                ) => collect($reflectionClass->getMethods(\ReflectionMethod::IS_STATIC))->filter(
                    static fn (\ReflectionMethod $reflectionMethod): bool => $reflectionMethod->isPublic()
                        && !str($reflectionMethod->getName())->is(collect([
                            Assert::class,
                            Dispatchable::class,
                            Enumerable::class,
                            EnumeratesValues::class,
                            Facade::class,
                            Macroable::class,
                            ServiceProvider::class,
                            SymfonyCommand::class,
                        ])->map(
                            static fn (string $exceptClass) => collect(
                                (new \ReflectionClass($exceptClass))->getMethods(\ReflectionMethod::IS_STATIC)
                            )->map(
                                static fn (\ReflectionMethod $reflectionMethod): string => $reflectionMethod->getName()
                            )
                        )->flatten())
                )
            )
            ->filter(static fn (Collection $methods) => $methods->isNotEmpty())
            ->pipe(static function (Collection $allMethods): Collection {
                $module = select(
                    label: 'Please select Module',
                    options: $allMethods
                        ->groupBy(static fn (Collection $methods, string $class) => str($class)->explode('\\')->get(1))
                        ->keys(),
                    scroll: 15,
                );

                return $allMethods->filter(
                    static fn (Collection $methods, string $class) => str($class)->contains($module)
                );
            })
            // ->dump()
            ->each(function (Collection $methods, string $class): void {
                $this->newLine();

                $this->components->twoColumnDetail(
                    "<info>$class</info>",
                    str((new \ReflectionClass($class))->getFileName())->remove(base_path('/'))
                );

                $methods->each(function (\ReflectionMethod $reflectionMethod): void {
                    $this->components->twoColumnDetail(
                        $reflectionMethod->getName(),
                        str($reflectionMethod->getFileName())
                            ->remove(base_path('/'))
                            ->append(':', $reflectionMethod->getStartLine())
                    );
                });
            });
    }
}
