<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Composer\Semver\Comparator;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Sleep;
use Spatie\Packagist\PackagistClient;
use Spatie\Packagist\PackagistUrlGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowUnsupportedRequiresCommand extends Command
{
    protected $signature = 'show-unsupported-requires';

    protected $description = 'Show unsupported requires.';

    private PackagistClient $packagist;

    /**
     * @noinspection ForgottenDebugOutputInspection
     * @noinspection DebugFunctionUsageInspection
     *
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $jsonTree = Process::run('composer show --format=json --installed --tree')
            ->throw()
            ->output();

        $requires = collect(File::json(base_path('composer.json')));

        collect(Collection::json($jsonTree)->get('installed'))
            ->reject(static fn (array $package) => str($package['name'])->is([
                // ...
            ]))
            ->filter(
                fn (array $package): bool => collect($package['requires'] ?? [])
                    ->filter(fn ($package): bool => $this->isUnsupported($package['name'], $package['version']))
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
                        static fn (array $package): bool => ! str($package['version'])->contains('dev'),
                        []
                    );

                    return collect($latestVersion['require'])
                        ->filter(fn (string $version, string $name): bool => $this->isUnsupported($name, $version))
                        ->isNotEmpty();
                } catch (\Throwable) {
                    dump($name);

                    return true;
                }
            })
            ->dump()
            ->tap(static fn (Collection $packages) => str($packages->implode(' '))->dump())
            ->groupBy(static function (string $name) use ($requires): string {
                if (\array_key_exists($name, $requires->get('require'))) {
                    return 'require';
                }

                if (\array_key_exists($name, $requires->get('require-dev'))) {
                    return 'require-dev';
                }

                return 'indirect';
            })
            ->each(static fn (Collection $packages) => str($packages->dump()->implode(' '))->dump());
    }

    /**
     * @noinspection MethodVisibilityInspection
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->packagist = new PackagistClient(
            new Client([RequestOptions::VERIFY => false]),
            new PackagistUrlGenerator
        );
    }

    private function isUnsupported(string $name, string $version): bool
    {
        if (! str($name)->is(['laravel/framework', 'illuminate/*'])) {
            return false;
        }

        $version = str($version);
        if ($version->trim()->startsWith(['>', '*'])) {
            return false;
        }

        $maxVersion = $version->explode('|')
            ->filter()
            ->map(
                static fn (string $version): ?string => str($version)
                    ->trim(" \n\r\t\v\0<=>^~v!.*")
                    ->explode('.')
                    ->first()
            )
            ->max();

        return Comparator::lessThan($maxVersion, '11');
    }
}
