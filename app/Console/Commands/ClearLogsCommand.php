<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ClearLogsCommand extends Command
{
    protected $signature = 'clear:logs {days=30 : The number of days to keep}';

    protected $description = 'Clear logs';

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
                    if (! $isDaily) {
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
