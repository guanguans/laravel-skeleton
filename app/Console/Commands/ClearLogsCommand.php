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

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class ClearLogsCommand extends Command
{
    /** @noinspection ClassOverridesFieldOfSuperClassInspection */
    #[\Override]
    protected $signature = 'clear:logs {days=30 : The number of days to keep}';

    /** @noinspection ClassOverridesFieldOfSuperClassInspection */
    #[\Override]
    protected $description = 'Clear logs';

    public function handle(): void
    {
        $files = array_keys(iterator_to_array(
            Finder::create()
                ->in(storage_path('logs/'))
                ->ignoreDotFiles(false)
                ->ignoreUnreadableDirs(false)
                ->ignoreVCS(true)
                ->ignoreVCSIgnored(false)
                ->name('*.log')
                ->filter(
                    fn (SplFileInfo $fileInfo): bool => preg_match('/^.*-\d{4}-\d{2}-\d{2}.log$/', $fileInfo->getBasename())
                        && Date::createFromTimestamp($fileInfo->getMTime())->diffInDays(now()) > (int) $this->argument('days')
                )
                ->files()
        ));

        $this->components->bulletList($files);
        File::delete($files);
    }

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function rules(): array
    {
        return [
            'days' => 'integer|min:7',
        ];
    }
}
