<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Dcat\Admin\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Stringable;

final class IdeHelperGatesCommand extends Command
{
    protected $signature = 'ide-helper:gates {--p|path=_ide_helper_gates.php : The path to the IDE helper file}';

    protected $description = 'Generate IDE helper file for gates.';

    public function isEnabled(): bool
    {
        return $this->laravel->isLocal();
    }

    /** @noinspection PhpMemberCanBePulledUpInspection */
    public function handle(): void
    {
        (new (config('admin.database.permissions_model'))())
            ->allNodes()
            ->tap(static function (Collection $permissions) use (&$count): void {
                $count = $permissions->count();
            })
            ->reduce(
                static fn (Stringable $code, Permission $permissions): Stringable => $code->append(
                    \sprintf("\%s::define('%s', 'callback');", Gate::class, $permissions['slug']),
                    PHP_EOL
                ),
                str(
                    <<<'PHP'
                        <?php

                        /** @noinspection all */

                        PHP
                )
            )
            ->tap(function (Stringable $code) use ($count): void {
                $path = str(base_path($this->option('path')))->finish('.php');

                File::ensureDirectoryExists($path->dirname());
                File::put($path->toString(), $code);

                $this->components->info("Generated IDE helper file for $count gates at $path.");
            });
    }

    protected function rules(): array
    {
        return [
            'path' => 'string|required',
        ];
    }
}
