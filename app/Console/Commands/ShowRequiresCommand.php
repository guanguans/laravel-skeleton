<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Composer\Semver\Comparator;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use Spatie\Packagist\PackagistClient;
use Spatie\Packagist\PackagistUrlGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowRequiresCommand extends Command
{
    protected $signature = 'show:requires';

    protected $description = 'Command description';

    private PackagistClient $packagist;

    public function handle(): void
    {
        $jsonTree = Process::run('composer show --format=json --installed --tree')
            ->throw()
            ->output();

        collect(Collection::json($jsonTree)->get('installed'))
            ->reject(static fn (array $package) => Str::is(
                [
                    // ...
                ],
                $package['name']
            ))
            ->filter(
                fn (array $package): bool => collect($package['requires'] ?? [])
                    ->filter(fn ($package): bool => $this->is($package['name'], $package['version']))
                    ->isNotEmpty()
            )
            ->pluck('name')
            ->dump()
            ->tap(static fn (Collection $packages) => str($packages->implode(' '))->dump())
            ->filter(function (string $name) {
                try {
                    Sleep::usleep(100);

                    $package = $this->packagist->getPackage($name);

                    $latestVersion = collect($package['package']['versions'])->first(
                        static fn ($package): bool => ! str($package['version'])->contains('dev'),
                        []
                    );

                    return collect($latestVersion['require'])
                        ->filter(fn (string $version, string $name): bool => $this->is($name, $version))
                        ->isNotEmpty();
                } catch (\Throwable) {
                    return true;
                }
            })
            ->dump()
            ->tap(static fn (Collection $packages) => str($packages->implode(' '))->dump());
    }

    /**
     * @noinspection MethodVisibilityInspection
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->packagist = new PackagistClient(
            new Client([RequestOptions::VERIFY => false]),
            new PackagistUrlGenerator()
        );
    }

    private function is(string $name, string $version): bool
    {
        if (! Str::is(['laravel/framework', 'illuminate/*'], $name)) {
            return false;
        }

        $version = str($version);
        if ($version->trim()->startsWith(['>', '*'])) {
            return false;
        }

        $maxVersion = $version->explode('|')->filter()->map(static fn (string $version): string => trim($version, " \n\r\t\v\0<=>^~v!"))->max();

        return Comparator::lessThan($maxVersion, '11');
    }
}
