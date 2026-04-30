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

use Illuminate\Console\Command;
use Illuminate\Support\Composer;

final class OptimizeAllCommand extends Command
{
    /** @noinspection ClassOverridesFieldOfSuperClassInspection */
    #[\Override]
    protected $signature = 'optimize:all {--f|force : Force optimize.}';

    /** @noinspection ClassOverridesFieldOfSuperClassInspection */
    #[\Override]
    protected $description = 'Optimize all.';

    /**
     * @noinspection PhpUndefinedMethodInspection
     */
    public function handle(): void
    {
        if (!$this->option('force') && $this->getLaravel()->isProduction()) {
            $this->components->warn('Please use --force option in production.');

            return;
        }

        $this->components->info('Optimizing all...');

        foreach (['config:cache', 'event:cache', 'route:cache', 'view:cache'] as $command) {
            try {
                $this->components->task($command, fn () => $this->call($command, ['--ansi' => true, '-v' => true]));
            } catch (\Throwable $throwable) {
                // $this->consoleLogger()->error($throwable->getMessage());
                $this->components->error($throwable->getMessage());
            }
        }

        try {
            $command = [
                ...resolve(Composer::class)->findComposer(),
                'dump-autoload', '--no-interaction', '--optimize', '--ansi', '-v',
            ];

            // $this->components->task('composer:dump-autoload-optimize', fn () => resolve(Composer::class)->dumpOptimized());
            $this->components->task(implode(' ', $command), fn () => $this->processHelperMustRun($command));
        } catch (\Throwable $throwable) {
            $this->components->error($throwable->getMessage());
        }

        $this->components->success('All optimized.');
    }
}
