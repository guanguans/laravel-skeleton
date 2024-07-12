<?php

/** @noinspection PhpUnusedPrivateMethodInspection */

declare(strict_types=1);

namespace App\Console\Commands;

use App\Rules\Rule;
use App\Support\Discover;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use ReflectionMethod;
use ReflectionObject;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class IdeHelperChoresCommand extends Command
{
    protected $signature = 'ide-helper:chores
        {--only=* : Only output chores with the given name}
        {--except=* : Do not output chores with the given name}
        {--json : Output as JSON.}
    ';

    protected $description = 'Generate chores for the Laravel-Idea-JSON file.';

    private array $only = [];

    private array $except = [];

    public function isEnabled(): bool
    {
        return $this->laravel->isLocal();
    }

    /** @noinspection PhpMemberCanBePulledUpInspection */
    public function handle(): void
    {
        collect((new ReflectionObject($this))->getMethods(ReflectionMethod::IS_PRIVATE))
            ->filter(static fn (ReflectionMethod $method) => str($method->name)->endsWith('Chore'))
            ->when($this->only, fn (Collection $methods) => $methods->filter(
                fn (ReflectionMethod $method) => str($method->name)->is($this->only)
            ))
            ->when($this->except, fn (Collection $methods) => $methods->reject(
                fn (ReflectionMethod $method) => str($method->name)->is($this->except)
            ))
            ->sortBy(static fn (ReflectionMethod $method) => $method->name)
            ->each(fn (ReflectionMethod $method) => $this->{$method->name}())
            ->tap(fn (Collection $methods) => $this->output->success("Generated {$methods->count()} chores."));
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->only = array_merge($this->only, $this->option('only'));
        $this->except = array_merge($this->except, $this->option('except'));
    }

    protected function rules(): array
    {
        return [
            'only' => 'array',
            'only.*' => 'string',
            'except' => 'array',
            'except.*' => 'string',
            'json' => 'bool',
        ];
    }

    private function routeUriChore(): void
    {
        collect(app(Router::class)->getRoutes())
            ->map(static fn (Route $route): string => $route->uri())
            ->unique()
            ->sort()
            ->values()
            ->tap(function (Collection $routeUris): void {
                $this->output->warning("Found {$routeUris->count()} route URIs.");
                $this->output($routeUris);
            });
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function ruleChore(): void
    {
        Discover::in('Rules')
            ->instanceOf(Rule::class)
            ->all()
            ->map(static fn (\ReflectionClass $ruleReflectionClass, $ruleClass): string => $ruleClass::name())
            ->sort()
            ->values()
            ->tap(function (Collection $rules): void {
                $this->output->warning("Found {$rules->count()} rules.");
                $this->output($rules);
            });
    }

    private function output(Collection $collection): void
    {
        $this->option('json')
            ? $this->line($collection->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS))
            : $this->output->listing($collection->all());
    }
}
