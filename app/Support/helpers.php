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

use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Composer\Autoload\ClassLoader;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\VarDumper;

if (!\function_exists('env_explode')) {
    /**
     * @noinspection LaravelFunctionsInspection
     */
    function env_explode(string $key, mixed $default = null, string $delimiter = ',', int $limit = \PHP_INT_MAX): mixed
    {
        $env = env($key, $default);

        if (\is_string($env)) {
            return $env ? explode($delimiter, $env, $limit) : [];
        }

        return $env;
    }
}

if (!\function_exists('env_getcsv')) {
    /**
     * @noinspection LaravelFunctionsInspection
     */
    function env_getcsv(
        string $key,
        mixed $default = null,
        string $delimiter = ',',
        string $enclosure = '"',
        string $escape = '\\'
    ): mixed {
        $env = env($key, $default);

        if (\is_string($env)) {
            return $env ? str_getcsv($env, $delimiter, $enclosure, $escape) : [];
        }

        return $env;
    }
}

if (!\function_exists('env_json_decode')) {
    /**
     * @noinspection LaravelFunctionsInspection
     */
    function env_json_decode(
        string $key,
        mixed $default,
        int $depth = 512,
        int $options = \JSON_THROW_ON_ERROR
    ): mixed {
        $env = env($key, $default);

        if (\is_string($env)) {
            return $env ? json_decode($env, true, $depth, $options) : [];
        }

        return $env;
    }
}

if (!\function_exists('classes')) {
    /**
     * @see \get_declared_classes()
     * @see \get_declared_interfaces()
     * @see \get_declared_traits()
     * @see \DG\BypassFinals::enable()
     *
     * @noinspection PhpUndefinedNamespaceInspection
     */
    function classes(): Collection
    {
        /** @var list<list<string>> $classes */
        static $classes = [];

        if ($classes) {
            return collect($classes);
        }

        foreach (spl_autoload_functions() as $loader) {
            if (\is_array($loader) && $loader[0] instanceof ClassLoader) {
                $classes[] = array_keys($loader[0]->getClassMap());
            }
        }

        $classes = array_unique(array_merge(
            // get_declared_classes(),
            // get_declared_interfaces(),
            // get_declared_traits(),
            ...$classes
        ));

        return collect($classes);
    }
}

if (!\function_exists('str_random')) {
    /**
     * @throws \Random\RandomException
     */
    function str_random(int $length = 16): string
    {
        return substr(bin2hex(random_bytes((int) ceil($length / 2))), 0, $length);
    }
}

if (!\function_exists('mysql_timezone_offset')) {
    /**
     * Gets the time offset from the provided timezone relative to UTC as a number. This
     * is used in the database configuration since we can't always rely on there being support
     * for named timezones in MySQL.
     *
     * Returns the timezone as a string like +08:00 or -05:00 depending on the app timezone.
     *
     * @see https://github.com/pelican-dev/panel/blob/3.x/app/Helpers/Time.php
     */
    function mysql_timezone_offset(?string $timezone = null): string
    {
        return CarbonImmutable::now($timezone ?: config('app.timezone'))->getTimezone()->toOffsetName();
    }
}

if (!\function_exists('deference')) {
    /**
     * @see https://github.com/php-defer/php-defer
     */
    function deference(?SplStack &$context, callable $callback): void
    {
        $context ??= new class extends SplStack {
            public function __destruct()
            {
                while ($this->count() > 0) {
                    ($this->pop())();
                }
            }
        };

        $context->push($callback);
    }
}

if (!\function_exists('humans_milliseconds')) {
    /**
     * @throws \Exception
     */
    function humans_milliseconds(float|int $milliseconds, ?array $syntax = null): string
    {
        return CarbonInterval::microseconds($milliseconds * 1000)
            ->cascade()
            ->forHumans($syntax ?? [
                'join' => ', ',
                'locale' => 'en',
                // 'locale' => 'zh_CN',
                'minimumUnit' => 'us',
                'short' => true,
            ]);
    }
}

if (!\function_exists('make')) {
    /**
     * @see https://github.com/laravel/framework/blob/12.x/src/Illuminate/Foundation/helpers.php
     * @see https://github.com/yiisoft/yii2/blob/master/framework/BaseYii.php
     *
     * @template TClass of object
     *
     * @param array<string, mixed>|class-string<TClass>|string $name
     * @param array<string, mixed> $parameters
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return ($name is class-string<TClass> ? TClass : mixed)
     */
    function make(array|string $name, array $parameters = []): mixed
    {
        if (\is_string($name)) {
            return resolve($name, $parameters);
        }

        foreach (
            $keys = [
                '__abstract',
                '__class',
                '__name',
                '_abstract',
                '_class',
                '_name',
                'abstract',
                'class',
                'name',
            ] as $key
        ) {
            if (isset($name[$key])) {
                return make($name[$key], $parameters + Arr::except($name, $key));
            }
        }

        throw new InvalidArgumentException(\sprintf(
            'The argument of abstract must be an array containing a `%s` element.',
            implode('` or `', $keys)
        ));
    }
}

if (!\function_exists('resolve_class_from')) {
    /**
     * @param string $path 文件路径
     * @param null|string $vendorPath 供应商路径
     * @param null|string $vendorNamespace 供应商命名空间
     */
    function resolve_class_from(string $path, ?string $vendorPath = null, ?string $vendorNamespace = null): string
    {
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $vendorPath = $vendorPath ? realpath($vendorPath) : app_path();
        $vendorNamespace ??= app()->getNamespace(); // App\

        return str(realpath($path))
            ->replaceFirst($vendorPath, $vendorNamespace)
            ->replaceLast('.php', '')
            ->replace(\DIRECTORY_SEPARATOR, '\\')
            ->replace('\\\\', '\\')
            ->start('\\')
            ->toString();
    }
}

if (!\function_exists('partical')) {
    /**
     * 偏函数.
     */
    function partical(callable $function, ...$args): callable
    {
        return static fn (...$moreArgs) => $function(...$args, ...$moreArgs);
    }
}

if (!\function_exists('curry')) {
    /**
     * 柯里化函数.
     */
    function curry(callable $function): callable
    {
        $accumulator = static fn ($arguments): Closure => static function (...$args) use (
            $arguments,
            $function,
            &$accumulator
        ) {
            $arguments = array_merge($arguments, $args);
            $reflection = new ReflectionFunction($function);
            $totalArguments = $reflection->getNumberOfRequiredParameters();

            if (\count($arguments) >= $totalArguments) {
                return $function(...$arguments);
            }

            return $accumulator($arguments);
        };

        return $accumulator([]);
    }
}

if (!\function_exists('compose')) {
    /**
     * 合成函数.
     */
    function compose(callable ...$functions): callable
    {
        return array_reduce(
            $functions,
            static fn (callable $carry, callable $function): Closure => static fn ($x) => $function($carry($x)),
            static fn ($x) => $x
        );
    }
}

if (!\function_exists('catch_query_log')) {
    function catch_query_log(callable|string $callback, ...$parameters): array
    {
        return (new Pipeline(app()))
            ->send($callback)
            ->through(static function ($callback, Closure $next) {
                DB::enableQueryLog();
                DB::flushQueryLog();

                $queryLog = $next($callback);

                DB::disableQueryLog();

                return $queryLog;
            })
            ->then(static function ($callback) use ($parameters) {
                $callback(...$parameters);

                return DB::getRawQueryLog();
            });
    }
}

if (!\function_exists('dump_to_server')) {
    /**
     * @see \Symfony\Component\VarDumper\VarDumper::register()
     *
     * ```
     * ./vendor/bin/var-dump-server
     * ```.
     *
     * @noinspection GlobalVariableUsageInspection
     * @noinspection PhpUndefinedClassInspection
     * @noinspection ForgottenDebugOutputInspection
     * @noinspection DebugFunctionUsageInspection
     * @noinspection StaticClosureCanBeUsedInspection
     * @noinspection AnonymousFunctionStaticInspection
     */
    function dump_to_server(mixed ...$vars): mixed
    {
        $_SERVER['VAR_DUMPER_FORMAT'] = 'server';
        // $_SERVER['VAR_DUMPER_SERVER'] = '0.0.0.0:9912';

        (fn (): null => self::$handler = null)->call(new VarDumper);

        return dump(...$vars);
    }
}

if (!\function_exists('pd')) {
    function pd(...$vars): never
    {
        pp(...$vars);

        exit(1);
    }
}

if (!\function_exists('pp')) {
    /**
     * @noinspection DebugFunctionUsageInspection
     */
    function pp(...$vars): void
    {
        foreach ($vars as $var) {
            highlight_string(\sprintf("\n<?php\n\$var = %s;\n?>\n", var_export($var, true)));
        }
    }
}

if (!\function_exists('user_http_build_query')) {
    /**
     * http_build_query 的实现。
     *
     * ```
     * $queryPayload = [
     *     1 => 'a',
     *     '10' => 'b',
     *     '01' => 'c',
     *     'keyO1' => null,
     *     'keyO2' => false,
     *     'keyO3' => true,
     *     'keyO4' => 0,
     *     'keyO5' => 1,
     *     'keyO6' => 0.0,
     *     'keyO7' => 0.1,
     *     'keyO8' => [],
     *     'keyO9' => '',
     *     'key10' => new \stdClass(),
     *     'pastimes' => ['golf', 'opera', 'poker', 'rap'],
     *     'user' => [
     *         'name' => 'Bob Smith',
     *         'age' => 47,
     *         'sex' => 'M',
     *         'dob' => '5/12/1956'
     *     ],
     *     'children' => [
     *         'sally' => ['age' => 8, 'sex' => null],
     *         'bobby' => ['sex' => 'M', 'age' => 12],
     *     ],
     * ];
     * ```
     *
     * @noinspection PhpVariableNamingConventionInspection
     */
    function user_http_build_query(array $queryPayload, string $numericPrefix = '', string $argSeparator = '&', int $encType = \PHP_QUERY_RFC1738): string
    {
        /**
         * 转换值是非标量的情况.
         *
         * @param array|object $value
         */
        $toQueryStr = static function (string $key, mixed $value, string $argSeparator, int $encType) use (&$toQueryStr): string {
            $queryStr = '';

            foreach ($value as $k => $v) {
                // 特殊值处理
                if (null === $v) {
                    continue;
                }

                if (0 === $v || false === $v) {
                    $v = '0';
                }

                $fullKey = "{$key}[$k]";
                $queryStr .= \is_scalar($v)
                    ? \sprintf("%s=%s$argSeparator", \PHP_QUERY_RFC3986 === $encType ? rawurlencode($fullKey) : urlencode($fullKey), \PHP_QUERY_RFC3986 === $encType ? rawurlencode($v) : urlencode($v))
                    : $toQueryStr($fullKey, $v, $argSeparator, $encType); // 递归调用
            }

            return $queryStr;
        };

        reset($queryPayload);
        $queryStr = '';

        foreach ($queryPayload as $k => $v) {
            // 特殊值处理
            if (null === $v) {
                continue;
            }

            if (0 === $v || false === $v) {
                $v = '0';
            }

            // 为了对数据进行解码时获取合法的变量名
            if (is_numeric($k) && !\is_string($k)) {
                $k = $numericPrefix.$k;
            }

            $queryStr .= \is_scalar($v)
                ? \sprintf("%s=%s$argSeparator", \PHP_QUERY_RFC3986 === $encType ? rawurlencode($k) : urlencode($k), \PHP_QUERY_RFC3986 === $encType ? rawurlencode($v) : urlencode($v))
                : $toQueryStr($k, $v, $argSeparator, $encType);
        }

        return substr($queryStr, 0, -\strlen($argSeparator));
    }
}
