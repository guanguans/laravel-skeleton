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
        // $scripts = Collection::fromJson(file_get_contents(base_path('composer.json')))
        //     ->only(['scripts', 'scripts-aliases'])
        //     ->dd();

        $readmePath = str($this->argument('path') ?? base_path('README.md'));
        $tree = Process::run(['tree', app_path()])
            ->throw()
            ->output();

        str(File::get($readmePath))
            ->replaceMatches(
                '/```tree\n.*\n```/s',
                <<<TREE
                    ```tree
                    $tree
                    ```
                    TREE
            )
            // ->dd()
            ->tap(static fn (Stringable $readme): bool|int => File::put($readmePath, $readme));
    }

    #[\Override]
    protected function rules(): array
    {
        return [
            'path' => 'nullable|string',
        ];
    }
}
