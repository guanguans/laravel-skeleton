<?php

namespace App\Providers;

use App\Rules\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerNotProductionServices();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('zh');
        $this->registerRequestMacros();
        $this->extendValidators();
    }

    /**
     * Register rule.
     */
    protected function extendValidators()
    {
        $files = (new Finder())
            ->files()
            ->name('*Rule.php')
            ->in($this->app->path('Rules'));

        foreach ($files as $file) {
            $ruleClass = value(function (SplFileInfo $file, $basePath) {
                $class = trim(Str::replaceFirst($basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

                return str_replace(
                    [DIRECTORY_SEPARATOR, ucfirst(basename(app()->path())).'\\'],
                    ['\\', app()->getNamespace()],
                    ucfirst(Str::replaceLast('.php', '', $class))
                );
            }, $file, base_path());

            try {
                /* @var \App\Rules\Rule $rule */
                $rule = app($ruleClass);
                if (! $rule instanceof Rule) {
                    continue;
                }
            } catch (Throwable $e) {
                continue;
            }

            Validator::extend($rule->getName(), "$ruleClass@passes", $rule->message());
        }
    }

    /**
     * Register local services.
     */
    protected function registerNotProductionServices()
    {
        if ($this->app->isProduction()) {
            return;
        }

        $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        $this->app->register(\Knuckles\Scribe\ScribeServiceProvider::class);
        $this->app->register(\NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider::class);
    }

    /**
     * Register request macros.
     */
    protected function registerRequestMacros()
    {
        Request::macro('strictInput', function ($keys = null) {
            $input = $this->getInputSource()->all();

            if (! $keys) {
                return $input;
            }

            $results = [];

            foreach (is_array($keys) ? $keys : func_get_args() as $key) {
                Arr::set($results, $key, Arr::get($input, $key));
            }

            return $results;
        });

        Request::macro('strictAll', function ($keys = null) {
            $input = array_replace_recursive($this->strictInput(), $this->allFiles());

            if (! $keys) {
                return $input;
            }

            $results = [];

            foreach (is_array($keys) ? $keys : func_get_args() as $key) {
                Arr::set($results, $key, Arr::get($input, $key));
            }

            return $results;
        });

        Request::macro('validateStrictAll', function (array $rules, ...$params) {
            return validator()->validate($this->strictAll(), $rules, ...$params);
        });

        Request::macro('validateStrictAllWithBag', function (string $errorBag, array $rules, ...$params) {
            try {
                return $this->validateStrictAll($rules, ...$params);
            } catch (ValidationException $e) {
                $e->errorBag = $errorBag;

                throw $e;
            }
        });
    }
}
