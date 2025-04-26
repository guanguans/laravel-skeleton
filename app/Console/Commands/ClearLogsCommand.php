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

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class ClearLogsCommand extends Command
{
    protected $signature = 'clear:logs {days=30 : The number of days to keep}';
    protected $description = 'Clear logs';

    /**
     * @noinspection PhpMemberCanBePulledUpInspection
     */
    public function handle(): void
    {
        $files = array_keys(iterator_to_array(
            Finder::create()
                ->in(storage_path('logs'))
                ->ignoreDotFiles(true)
                ->ignoreVCS(true)
                ->name('*.log')
                ->filter(function (SplFileInfo $fileInfo): bool {
                    $isDaily = preg_match('/^.*-\d{4}-\d{2}-\d{2}.log$/', $fileInfo->getBasename());

                    if (!$isDaily) {
                        return false;
                    }

                    return Date::createFromTimestamp($fileInfo->getMTime())->diffInDays(now()) > $this->argument('days');
                })
                ->files()
        ));

        $this->output->listing($files);
        File::delete($files);
    }

    #[\Override]
    protected function rules(): array
    {
        return [
            'days' => 'integer|min:7',
        ];
    }
}
