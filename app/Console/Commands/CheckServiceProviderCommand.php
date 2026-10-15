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
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[\Illuminate\Console\Attributes\Description('Check service providers and ensure they are correctly registered.')]
#[\Illuminate\Console\Attributes\Signature(
    <<<'SIGNATURE'
        check:service-provider
        {--except=* : The list of packages to exclude from the service provider check.}
        {--ignore-indirect : Ignore indirect discovered packages.}
        SIGNATURE
)]
final class CheckServiceProviderCommand extends AbstractCommand
{
    /** @var list<string> */
    private array $except = [];

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(): int
    {
        $this->callSilently('package:discover');

        $composer = File::json(base_path('composer.json'), \JSON_THROW_ON_ERROR);
        $prodPackages = array_keys($composer['require'] ?? []);
        $devPackages = array_keys($composer['require-dev'] ?? []);
        $dontDiscoverPackages = $composer['extra']['laravel']['dont-discover'] ?? [];

        /** @noinspection UsingInclusionReturnValueInspection */
        $discoveredPackages = collect(require base_path('bootstrap/cache/packages.php'))->reject(
            fn (array $_, string $package): bool => str($package)->is($this->except)
        );
        $shouldntDiscoverPackages = $discoveredPackages->filter(static fn (
            array $_,
            string $package
        ): bool => \in_array($package, $devPackages, true) && !\in_array($package, $dontDiscoverPackages, true));
        $indirectDiscoveredPackages = $discoveredPackages
            ->filter(static fn (
                array $_,
                string $package
            ): bool => !\in_array($package, $prodPackages, true) && !\in_array($package, $devPackages, true))
            ->when($this->option('ignore-indirect'), static fn (): Collection => Collection::empty());

        if ($shouldntDiscoverPackages->isNotEmpty() || $indirectDiscoveredPackages->isNotEmpty()) {
            $this->warn(\sprintf(
                <<<'WARN'
                    The dev packages should be added to `extra.laravel.dont-discover` in `composer.json`:
                    %s

                    The dev service providers should be manually registered to dev environment:
                    %s

                    The indirect discovered packages should be manually handled:
                    %s
                    %s
                    %s
                    WARN,
                $shouldntDiscoverPackages->keys()->pipe(
                    $piper = static fn (Collection $collection) => $collection->sort()->values()->toJson(
                        \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES
                    )
                ),
                $shouldntDiscoverPackages->pluck('providers')->flatten()->pipe($piper),
                $indirectDiscoveredPackages->pipe($piper),
                $indirectDiscoveredPackages->keys()->pipe($piper),
                $indirectDiscoveredPackages->pluck('providers')->flatten()->pipe($piper),
            ));

            return 1;
        }

        $this->components->info('All service providers are correctly registered.');

        return 0;
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->except = [...$this->except, ...(array) $this->option('except')];
    }

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function rules(): array
    {
        return [
            'except' => 'list',
            'except.*' => 'required|string|distinct',
            'ignore-indirect' => 'boolean',
        ];
    }
}
