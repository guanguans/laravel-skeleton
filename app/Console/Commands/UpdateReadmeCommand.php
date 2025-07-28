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
use Symfony\Component\Process\ExecutableFinder;

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
        $this->updateComposerScripts();
        $this->updatePackages();
        $this->updateAppTree();

        Process::run(
            [
                (new ExecutableFinder)->find('git', 'git'),
                'diff',
                '--color',
                $this->readmePath(),
            ],
            fn (string $type, string $line): null => $this->output->write($line)
        )->throw();
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
    private function updateComposerScripts(): void
    {
        $composerScripts = $this->composerJsonCollection()
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

        $this->replaceMatchesReadmeDetails(
            'Composer scripts',
            <<<COMPOSER_SCRIPTS
                ```shell
                $composerScripts
                ```
                COMPOSER_SCRIPTS
        );
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function updatePackages(): void
    {
        $packages = Collection::fromJson(
            Process::run([
                ...resolve(Composer::class)->findComposer((new ExecutableFinder)->find('composer')),
                'show',
                '--format=json',
                '--direct',
            ])
                ->throw()
                ->output()
        )
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
            ->map(
                static fn (array $package): string => str(\sprintf(
                    '* [%s](%s)',
                    $package['name'],
                    str($package['source'])->whenEndsWith(
                        $package['version'],
                        static fn (Stringable $source): Stringable => $source->replaceEnd("/tree/{$package['version']}", '')
                    )
                ))->when(
                    $package['description'],
                    static fn (Stringable $str, string $description) => $str->append(" - $description")
                )->toString()
            )
            ->implode(\PHP_EOL);

        // 防止异常情况导致清空
        if (blank($packages)) {
            return;
        }

        $this->replaceMatchesReadmeDetails(
            'Packages',
            <<<PACKAGES
                $packages
                PACKAGES
        );
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function updateAppTree(): void
    {
        $appTree = Process::path(base_path())
            ->run([(new ExecutableFinder)->find('tree', 'tree'), 'app', '--charset', 'HTML', '-F'])
            ->throw()
            ->output();

        $this->replaceMatchesReadmeDetails(
            'App tree',
            <<<APP_TREE
                ```shell
                $appTree
                ```
                APP_TREE
        );
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function replaceMatchesReadmeDetails(string $summary, string $details, int $limit = -1): void
    {
        str(File::get($readmePath = $this->readmePath()))
            ->replaceMatches(
                "/<details>\\n<summary>$summary<\\/summary>\\n.*\\n<\\/details>/sU",
                <<<DETAILS
                    <details>
                    <summary>$summary</summary>

                    $details

                    </details>
                    DETAILS,
                $limit
            )
            ->tap(static fn (Stringable $readmeContent): bool|int => File::put($readmePath, $readmeContent));
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function composerJsonCollection(): Collection
    {
        static $collection;

        return $collection ?? Collection::fromJson(File::get(base_path('composer.json')));
    }

    private function readmePath(): string
    {
        return $this->argument('path') ?? base_path('README.md');
    }
}
