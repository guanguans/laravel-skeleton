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
    protected $signature = <<<'EOF'
        show-unsupported-requires
        {--package=* : The name of package.}
        {--major-version=12 : The minimum major version of the package is required.}
        EOF;
    protected $description = 'Show unsupported requires.';
    private PackagistClient $packagist;

    /**
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
                Sleep::usleep(100);

                try {
                    $package = $this->packagist->getPackage($name);
                } catch (\Throwable $throwable) {
                    $this->components->warn("$name [{$throwable->getMessage()}]");

                    return true;
                }

                $latestVersion = collect($package['package']['versions'])->first(
                    static fn (array $package): bool => !str($package['version'])->contains('dev'),
                    []
                );

                return collect($latestVersion['require'])
                    ->filter(fn (string $version, string $name): bool => $this->isUnsupported($name, $version))
                    ->isNotEmpty();
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
        parent::initialize($input, $output);
        $this->packagist = new PackagistClient(
            new Client([RequestOptions::VERIFY => false]),
            new PackagistUrlGenerator
        );
    }

    private function isUnsupported(string $name, string $version): bool
    {
        if (!str($name)->is($this->option('package') ?: ['laravel/framework', 'illuminate/*'])) {
            return false;
        }

        $version = str($version);

        if ($version->trim()->startsWith(['>', '*'])) {
            return false;
        }

        $majorVersion = $version->explode('|')
            ->filter()
            ->map(
                static fn (string $version): ?string => str($version)
                    ->trim(" \n\r\t\v\0<=>^~v!.*")
                    ->explode('.')
                    ->first()
            )
            ->max();

        return Comparator::lessThan($majorVersion, $this->option('major-version'));
    }
}
