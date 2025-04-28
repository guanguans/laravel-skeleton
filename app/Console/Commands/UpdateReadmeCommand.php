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

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Stringable;

final class UpdateReadmeCommand extends Command
{
    protected $signature = 'readme:update {path? : The path of readme}';
    protected $description = 'Update readme';

    /**
     * @noinspection PhpMemberCanBePulledUpInspection
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(): void
    {
        $this->updateScript();
        $this->updatePackage();
        $this->updateTree();
    }

    #[\Override]
    protected function rules(): array
    {
        return [
            'path' => 'nullable|string',
        ];
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function updateScript(): void
    {
        $scripts = $this->composerJsonCollection()
            ->pipe(static fn (Collection $collection): Collection => collect([
                ...array_keys($collection->get('scripts', [])),
                ...Arr::flatten($collection->get('scripts-aliases')),
            ]))
            ->reject(static fn (string $script): bool => str($script)->is([
                'post-*',
                'pre-*',
            ]))
            ->map(static fn (string $script): Stringable => str($script)->prepend('composer '))
            ->sort()
            ->implode(\PHP_EOL);

        $this->replaceMatchesReadme(
            /** @lang PhpRegExp */
            '/```script\n.*\n```/sU',
            <<<SCRIPT
                ```script
                $scripts
                ```
                SCRIPT
        );
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function updatePackage(): void
    {
        $packages = Process::run([
            ...resolve(Composer::class)->findComposer(),
            'show',
            '--format=json',
            '--direct',
        ])
            ->throw()
            ->output();

        $list = Collection::fromJson($packages)
            ->collapse()
            ->sort(function (array $a, array $b): int {
                $require = $this->composerJsonCollection()->get('require', []);
                $requireDev = $this->composerJsonCollection()->get('require-dev', []);

                if (isset($require[$a['name']], $requireDev[$b['name']])) {
                    return -1;
                }

                if (isset($requireDev[$a['name']], $require[$b['name']])) {
                    return 1;
                }

                return strcmp($a['name'], $b['name']);
            })
            ->map(static fn (array $package): string => \sprintf(
                '* [%s](%s) - %s',
                $package['name'],
                str($package['source'])->whenEndsWith(
                    $package['version'],
                    static fn (Stringable $source): Stringable => $source->replaceEnd("/tree/{$package['version']}", '')
                ),
                $package['description']
            ))
            ->implode(\PHP_EOL);

        $this->replaceMatchesReadme(
            /** @lang PhpRegExp */
            '/<!--package-start-->\n.*\n<!--package-end-->/sU',
            <<<PACKAGE
                <!--package-start-->
                $list
                <!--package-end-->
                PACKAGE
        );
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function updateTree(): void
    {
        $tree = Process::run(['tree', app_path()])->throw()->output();

        $this->replaceMatchesReadme(
            /** @lang PhpRegExp */
            '/```tree\n.*\n```/sU',
            <<<TREE
                ```tree
                $tree
                ```
                TREE
        );
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function replaceMatchesReadme(array|string $pattern, array|\Closure|string $replace, int $limit = -1): void
    {
        str(File::get($readmePath = ($this->argument('path') ?? base_path('README.md'))))
            ->replaceMatches($pattern, $replace, $limit)
            ->tap(static fn (Stringable $readmeContent): bool|int => File::put($readmePath, $readmeContent));
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function composerJsonCollection(): Collection
    {
        static $composerJsonCollection;

        return $composerJsonCollection ?? Collection::fromJson(File::get(base_path('composer.json')));
    }
}
