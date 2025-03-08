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

/** @noinspection MethodVisibilityInspection */

namespace App\Console\Commands\Concerns;

use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

/**
 * @mixin \Illuminate\Console\Command
 */
trait Configureable
{
    public function getDefinition(): InputDefinition
    {
        return tap(parent::getDefinition(), static function (InputDefinition $definition): void {
            $definition->addOption(new InputOption(
                'config',
                'c',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Configure able (e.g. `--config=app.name=guanguans` or `--config app.name=guanguans` or `-c app.name=guanguans`)',
            ));
        });
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);

        collect($this->option('config'))
            // ->dump()
            ->mapWithKeys(static function ($config): array {
                Assert::contains($config, '=', "The configureable option [$config] must be formatted as key=value.");

                [$key, $value] = str($config)->explode('=', 2)->all();

                return [$key => $value];
            })
            ->tap(static function (Collection $config): void {
                config($config->all());
            });
    }
}
