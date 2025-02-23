<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
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
