<?php

namespace App\Providers;

use AaronFrancis\Solo\Commands\EnhancedTailCommand;
use AaronFrancis\Solo\Facades\Solo;
use AaronFrancis\Solo\Providers\SoloApplicationServiceProvider;

class SoloServiceProvider extends SoloApplicationServiceProvider
{
    public function register(): void
    {
        Solo::useTheme('dark')
            // FQCNs of trusted classes that can add commands.
            ->allowCommandsAddedFrom([
                //
            ])
            // Commands that auto start.
            ->addCommands([
                EnhancedTailCommand::make('Logs', 'tail -f -n 100 '.storage_path('logs/laravel.log')),
                'Vite' => 'npm run dev',
                // 'HTTP' => 'php artisan serve',
                'About' => 'php artisan solo:about',
            ])
            // Not auto-started
            ->addLazyCommands([
                'Queue' => 'php artisan queue:listen --tries=1',
                // 'Reverb' => 'php artisan reverb:start',
                // 'Pint' => 'pint --ansi',
            ]);
    }

    public function boot(): void
    {
        //
    }
}
