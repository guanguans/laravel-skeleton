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

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Console\Prohibitable;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Contracts\Console\PromptsForMissingInput;

final class ClearAllCommand extends Command implements Isolatable, PromptsForMissingInput
{
    use ConfirmableTrait;
    use Prohibitable;
    protected $signature = 'clear:all {--f|force : Force clear optimized.}';
    protected $aliases = ['ca'];
    protected $description = 'clear optimized all.';

    public function handle(): int
    {
        /**
         * @see \Illuminate\Database\Console\Migrations\RefreshCommand
         */
        if (/* $this->isProhibited() || */ !$this->confirmToProceed()) {
            return self::FAILURE;
        }

        $this->output->info('⏳ Clearing all...');

        $this->call('config:clear', $arguments = ['--ansi' => true, '-v' => true]);
        $this->call('cache:clear', $arguments);
        $this->call('clear-compiled', $arguments);
        $this->call('event:clear', $arguments);
        $this->call('optimize:clear', $arguments);
        $this->call('route:clear', $arguments);
        $this->call('view:clear', $arguments);
        \function_exists('opcache_invalidate') and opcache_invalidate(app()->getCachedConfigPath());

        $this->output->success('✅ All cleared.');
        // $this->output->info('ℹ️ All cleared.');
        // $this->output->info('⏳ Please wait...');
        // $this->output->warning('⚠️ All cleared.');
        // $this->output->error('❌ All cleared.');
        // $this->output->success('✅ All cleared.');

        // $this->fail('❌ Whoops! looks like something went wrong.');

        // $this->trap([\SIGTERM, \SIGQUIT], function (int $signal): void {
        //     $this->shouldKeepRunning = false;
        //     dump($signal);
        // });

        return self::SUCCESS;
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'user' => 'Which user ID should receive the mail?',
        ];
    }
}
