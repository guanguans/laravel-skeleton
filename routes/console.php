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

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Number;
use Spatie\ImageOptimizer\OptimizerChain;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('model:prune')->daily()->withoutOverlapping();

Artisan::command(
    'optimize:image {--path=* : The list of image paths(directory or file)} {--dry-run : Only list found images}',
    function (): void {
        $imagesFinder = function (): Collection {
            $finder = Finder::create()
                ->exclude(['.build/', 'Fixtures/', 'vendor-bin/', 'vendor/'])
                ->name(['/\.avif$/', '/\.gif$/', '/\.jpeg$/', '/\.jpg$/', '/\.png$/', '/\.svg$/', '/\.webp$/'])
                ->size('> 0')
                ->files();

            return collect(
                $this->option('path')
                    ? array_reduce(
                        $this->option('path'),
                        static fn (
                            Finder $finder,
                            string $path
                        ): Finder => is_dir($path) ? $finder->in($path) : $finder->append([$path]),
                        $finder
                    )
                    : $finder
                        ->in(getcwd())
                        ->ignoreDotFiles(false)
                        ->ignoreUnreadableDirs(false)
                        ->ignoreVCS(true)
                        ->ignoreVCSIgnored(true)
            )->map(static function (SplFileInfo $image): array {
                $size = $image->getSize();
                \assert(\is_int($size));

                return [
                    'human_size' => Number::fileSize($size),
                    'real_path' => $image->getRealPath(),
                    'size' => $size,
                ];
            });
        };

        /** @var \Illuminate\Foundation\Console\ClosureCommand $this */
        if ($this->option('dry-run')) {
            $imagesFinder()
                ->tap(fn (Collection $images) => $this->components->info("Found {$images->count()} images:"))
                ->each(fn (array $image) => $this->components->twoColumnDetail($image['real_path'], $image['human_size']));

            return;
        }

        $imagesFinder()
            ->tap(fn (Collection $images) => $this->components->info("Optimizing {$images->count()} images:"))
            ->each(fn (array $image) => $this->components->task(
                $image['real_path'],
                static fn () => resolve(OptimizerChain::class)->optimize($image['real_path'])
            ))
            ->tap(fn (Collection $images) => $this->components->info("Optimization results for {$images->count()} images:"))
            ->tap(fn (Collection $images) => $imagesFinder()->each(function (array $fileInfo, string $file) use ($images): void {
                /** @var array{human_size: string, real_path: string, size: int} $originalImage */
                $originalImage = $images->get($file);
                $originalSize = $originalImage['size'];
                // $percentage = \sprintf('(%.2f%%)', ($originalSize - $fileInfo['size']) / $originalSize * 100);
                $percentage = Number::percentage(($originalSize - $fileInfo['size']) / $originalSize * 100, 1);
                $this->components->twoColumnDetail(
                    $file,
                    "{$originalImage['human_size']} -> {$fileInfo['human_size']} ($percentage)"
                );
            }));
    }
);
