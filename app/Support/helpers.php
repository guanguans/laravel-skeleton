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

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Cron\CronExpression;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Stringable;
use SebastianBergmann\Timer\ResourceUsageFormatter;
use SebastianBergmann\Timer\Timer;
use Symfony\Component\VarDumper\VarDumper;

if (!\function_exists('str_random')) {
    /**
     * @throws \Random\RandomException
     */
    function str_random(int $length = 16): string
    {
        return substr(bin2hex(random_bytes((int) ceil($length / 2))), 0, $length);
    }
}

if (!\function_exists('get_mysql_timezone_offset')) {
    /**
     * Gets the time offset from the provided timezone relative to UTC as a number. This
     * is used in the database configuration since we can't always rely on there being support
     * for named timezones in MySQL.
     *
     * Returns the timezone as a string like +08:00 or -05:00 depending on the app timezone.
     *
     * @see https://github.com/pelican-dev/panel/blob/main/app/Helpers/Time.php
     */
    function get_mysql_timezone_offset(?string $timezone = null): string
    {
        return CarbonImmutable::now($timezone ?: config('app.timezone'))->getTimezone()->toOffsetName();
    }
}

if (!\function_exists('get_mysql_timezone_offset')) {
    /**
     * Converts schedule cron data into a carbon object.
     *
     * @throws \Exception
     *
     * @see https://github.com/pelican-dev/panel/blob/main/app/Helpers/Time.php
     */
    function get_schedule_next_run_date(string $minute, string $hour, string $dayOfMonth, string $month, string $dayOfWeek): Carbon
    {
        return Carbon::instance(
            (new CronExpression(\sprintf('%s %s %s %s %s', $minute, $hour, $dayOfMonth, $month, $dayOfWeek)))->getNextRunDate()
        );
    }
}

if (!\function_exists('raw_sql_for')) {
    /**
     * @see \Illuminate\Database\Connection::getRawQueryLog()
     */
    function raw_sql_for(QueryExecuted $queryExecuted): string
    {
        return $queryExecuted->connection->getQueryGrammar()->substituteBindingsIntoRawSql(
            $queryExecuted->sql,
            $queryExecuted->connection->prepareBindings($queryExecuted->bindings)
        );
    }
}

if (!\function_exists('defers')) {
    /**
     * @see https://github.com/php-defer/php-defer
     */
    function defers(?SplStack &$context, callable $callback): void
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

if (!\function_exists('env_explode')) {
    /**
     * @noinspection LaravelFunctionsInspection
     */
    function env_explode(string $key, mixed $default = null, string $delimiter = ',', int $limit = \PHP_INT_MAX): mixed
    {
        $env = env($key, $default);

        return \is_string($env) ? explode($delimiter, $env, $limit) : $env;
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

        return \is_string($env) ? str_getcsv($env, $delimiter, $enclosure, $escape) : $env;
    }
}

if (!\function_exists('env_json_decode')) {
    /**
     * @noinspection LaravelFunctionsInspection
     */
    function env_json_decode(
        string $key,
        mixed $default,
        bool $assoc = true,
        int $depth = 512,
        int $options = \JSON_THROW_ON_ERROR
    ): mixed {
        $env = env($key, $default);

        return \is_string($env) ? json_decode($env, $assoc, $depth, $options) : $env;
    }
}

if (!\function_exists('human_bytes')) {
    /**
     * Convert bytes to human readable format.
     *
     * @param int $bytes the amount of bytes to convert to human readable format
     * @param int $decimals the number of decimals to use in the resulting string
     *
     * @see https://stackoverflow.com/a/23888858/1580028
     */
    function human_bytes(int $bytes, int $decimals = 2): string
    {
        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = (int) floor((\strlen((string) $bytes) - 1) / 3);

        if (0 === $factor) {
            $decimals = 0;
        }

        return \sprintf("%.{$decimals}f%s", $bytes / (1024 ** $factor), $size[$factor]);
    }
}

if (!\function_exists('human_milliseconds')) {
    function human_milliseconds(float $milliseconds, int $precision = 2): string
    {
        if (1 > $milliseconds) {
            return \sprintf('%sμs', round($milliseconds * 1000, $precision));
        }

        if (1000 > $milliseconds) {
            return \sprintf('%sms', round($milliseconds, $precision));
        }

        return \sprintf('%ss', round($milliseconds / 1000, $precision));
    }
}

if (!\function_exists('get_throwables')) {
    /**
     * @return list<\Throwable>
     */
    function get_throwables(Throwable $throwable): array
    {
        $throwables = [];

        while ($throwable instanceof Throwable) {
            $throwables[] = $throwable;
            $throwable = $throwable->getPrevious();
        }

        return $throwables;
    }
}

if (!\function_exists('matching')) {
    /**
     * This function is used to simulate the `match` expression of PHP 8.0.
     * This is just an example, don't call it in your code.
     *
     * @internal
     */
    function matching(mixed $value): bool
    {
        $switch = (static fn ($value): string => match (true) {
            'a' === $value, 'b' === $value => 'a or b',
            default => 'default',
        })($value);

        $match = match ($value) {
            'a','b' => 'a or b',
            default => 'default',
        };

        return $switch === $match;
    }
}

if (!\function_exists('home_dir')) {
    /**
     * @noinspection PhpComposerExtensionStubsInspection
     * @noinspection OffsetOperationsInspection
     */
    function home_dir(): string
    {
        return \function_exists('posix_getuid')
            ? posix_getpwuid(posix_getuid())['dir'] // Mac or Linux
            : exec('echo %USERPROFILE%'); // Windows
    }
}

if (!\function_exists('make')) {
    /**
     * @psalm-param string|array<string, mixed> $abstract
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \InvalidArgumentException
     */
    function make(mixed $abstract, array $parameters = []): mixed
    {
        throw_unless(\in_array(\gettype($abstract), ['string', 'array'], true), InvalidArgumentException::class, \sprintf('Invalid argument type(string/array): %s.', \gettype($abstract)));

        if (\is_string($abstract)) {
            return app($abstract, $parameters);
        }

        $classes = ['__class', '_class', 'class'];

        foreach ($classes as $class) {
            if (!isset($abstract[$class])) {
                continue;
            }

            $parameters = Arr::except($abstract, $class) + $parameters;
            $abstract = $abstract[$class];

            return make($abstract, $parameters);
        }

        throw new InvalidArgumentException(
            \sprintf('The argument of abstract must be an array containing a `%s` element.', implode('` or `', $classes))
        );
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

if (!\function_exists('resolve_facade_docblock')) {
    /**
     * @noinspection DebugFunctionUsageInspection
     * @noinspection PhpUndefinedMethodInspection
     * @noinspection NullPointerExceptionInspection
     * @noinspection PhpVoidFunctionResultUsedInspection
     */
    function resolve_facade_docblock(string $class): string
    {
        return collect((new ReflectionClass($class))->getMethods(ReflectionMethod::IS_PUBLIC))
            ->reject(static fn (ReflectionMethod $method): bool => str_starts_with($method->getName(), '__'))
            ->reduce(static function (Stringable $docblock, ReflectionMethod $method): Stringable {
                $parameters = collect($method->getParameters())
                    ->map(static function (ReflectionParameter $parameter): string {
                        $defaultValue = static function ($value): string {
                            if (null === $value) {
                                return 'null';
                            }

                            if ([] === $value) {
                                return '[]';
                            }

                            $varExport = var_export($value, true);

                            if (\is_scalar($value)) {
                                return $varExport;
                            }

                            return str($varExport)
                                ->remove(\PHP_EOL)
                                ->replace('array (  ', '[')
                                ->replace(',)', ']')
                                ->replace(',  ', ', ')
                                ->when(array_is_list($value), static fn (Stringable $stringable): Stringable => $stringable->remove(
                                    collect($value)->keys()->map(static fn (int $index): string => "$index => ")
                                ));
                        };

                        $type = str($parameter->getType()?->getName())
                            ->whenNotEmpty(static fn (Stringable $stringable): Stringable => $stringable->append(' '));

                        return $parameter->isDefaultValueAvailable()
                            ? \sprintf('%s$%s = %s', $type, $parameter->getName(), $defaultValue($parameter->getDefaultValue()))
                            : \sprintf('%s$%s', $type, $parameter->getName());
                    })
                    ->join(', ');

                $returnType = (static fn (ReflectionMethod $method): string => $method->getReturnType()?->getName()
                    ?: str($method->getDocComment())
                        ->match(/** @lang PHP */ '/^\s+\*\s+@return\s+\$?([\w|\\\]+)/m')
                        ->replace('this', 'self')
                    ?? 'mixed')($method);

                return $docblock
                    ->newLine()
                    ->append(\sprintf(' * @method static %s %s(%s)', $returnType, $method->getName(), $parameters));
            }, str(''))
            ->prepend('/**')
            ->append(<<<docblock

                 *
                 * @see \\$class
                 */
                docblock);
    }
}

if (!\function_exists('environment')) {
    function environment(): string
    {
        if (\defined('STDIN')) {
            return 'cli';
        }

        if ('cli' === \PHP_SAPI) {
            return 'cli';
        }

        if (false !== stripos(\PHP_SAPI, 'cgi') && getenv('TERM')) {
            return 'cli';
        }

        if (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && \count($_SERVER['argv']) > 0) {
            return 'cli';
        }

        return 'web';
    }
}

if (!\function_exists('format_bits')) {
    function format_bits(int $bits, $precision = 2, $suffix = true): float|int|string
    {
        if (0 < $bits) {
            $i = floor(log($bits) / log(1000));

            if (!$suffix) {
                return round($bits / (1000 ** $i), $precision);
            }

            $sizes = ['B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb'];

            return \sprintf('%.02F', round($bits / (1000 ** $i), $precision)) * 1 .' '.$sizes[$i];
        }

        return 0;
    }
}

if (!\function_exists('format_bytes')) {
    function format_bytes(int $bytes, $precision = 2): int|string
    {
        if (0 < $bytes) {
            $i = floor(log($bytes) / log(1024));

            $sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            return \sprintf('%.02F', round($bytes / (1024 ** $i), $precision)) * 1 .' '.$sizes[$i];
        }

        return 0;
    }
}

if (!\function_exists('bytes_to_bits')) {
    function bytes_to_bits(int $bytes): int
    {
        if (0 < $bytes) {
            return $bytes * 8;
        }

        return 0;
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
        $accumulator = static fn ($arguments): Closure => static function (...$args) use ($function, $arguments, &$accumulator) {
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

if (!\function_exists('memoize')) {
    function memoize(callable $function): callable
    {
        return static function (...$args) use ($function): array {
            static $cache = [];
            $key = serialize($args);
            $cached = true;

            if (!isset($cache[$key])) {
                $cache[$key] = $function(...$args);
                $cached = false;
            }

            return ['result' => $cache[$key], 'cached' => $cached];
        };
    }
}

if (!\function_exists('once')) {
    function once(callable $function): callable
    {
        return static function (...$args) use ($function) {
            static $called = false;

            if ($called) {
                return;
            }

            $called = true;

            return $function(...$args);
        };
    }
}

if (!\function_exists('is_json')) {
    /**
     * If the string is valid JSON, return true, otherwise return false.
     *
     * @param string $str the string to check
     *
     * @return bool the function is_json() is returning a boolean value
     */
    function is_json(string $str): bool
    {
        json_decode($str);

        return \JSON_ERROR_NONE === json_last_error();
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
     */
    function user_http_build_query(array $queryPayload, string $numericPrefix = '', string $argSeparator = '&', int $encType = \PHP_QUERY_RFC1738): string
    {
        /**
         * 转换值是非标量的情况.
         *
         * @param string $key
         * @param array|object $value
         * @param string $argSeparator
         * @param int $encType
         *
         * @return string
         */
        $toQueryStr = static function (string $key, $value, string $argSeparator, int $encType) use (&$toQueryStr): string {
            $queryStr = '';

            foreach ($value as $k => $v) {
                // 特殊值处理
                if (null === $v) {
                    continue;
                }

                if (0 === $v || false === $v) {
                    $v = '0';
                }

                $fullKey = "{$key}[{$k}]";
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

if (!\function_exists('validate')) {
    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    function validate(array $data = [], array $rules = [], array $messages = [], array $customAttributes = []): array
    {
        return validator($data, $rules, $messages, $customAttributes)->validate();
    }
}

if (!\function_exists('array_filter_filled')) {
    function array_filter_filled(array $array): array
    {
        return array_filter($array, static fn ($item) => filled($item));
    }
}

if (!\function_exists('call')) {
    /**
     * Call the given Closure / class@method and inject its dependencies.
     */
    function call(callable|string $callback, array $parameters = [], ?string $defaultMethod = null): mixed
    {
        return app()->call($callback, $parameters, $defaultMethod);
    }
}

if (!\function_exists('catch_resource_usage')) {
    function catch_resource_usage(callable|string $callback, ...$parameter): string
    {
        $timer = new Timer;
        $timer->start();

        app()->call($callback, $parameter);

        return (new ResourceUsageFormatter)->resourceUsage($timer->stop());
    }
}

if (!\function_exists('catch_query_log')) {
    function catch_query_log(callable|string $callback, ...$parameter): array
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
            ->then(static function ($callback) use ($parameter) {
                app()->call($callback, $parameter);

                return DB::getRawQueryLog();
            });
    }
}

if (!\function_exists('dump_to_server')) {
    /**
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

        (function (): void {
            self::$handler = null;
        })->call(new VarDumper);

        return dump(...$vars);
    }
}

if (!\function_exists('dump_to_array')) {
    /**
     * @noinspection ForgottenDebugOutputInspection
     * @noinspection DebugFunctionUsageInspection
     */
    function dump_to_array(...$vars): void
    {
        foreach ($vars as $var) {
            ($var instanceof Arrayable or method_exists($var, 'toArray')) ? dump($var->toArray()) : dump($var);
        }
    }
}

if (!\function_exists('dd_to_array')) {
    function dd_to_array(...$vars): never
    {
        dump_to_array(...$vars);

        exit(1);
    }
}

if (!\function_exists('array_reduce_with_keys')) {
    /**
     * @return null|mixed
     */
    function array_reduce_with_keys(array $array, callable $callback, mixed $carry = null): mixed
    {
        foreach ($array as $key => $value) {
            $carry = $callback($carry, $value, $key);
        }

        return $carry;
    }
}

if (!\function_exists('array_map_with_keys')) {
    function array_map_with_keys(callable $callback, array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $assoc = $callback($value, $key);

            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return $result;
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
    function pp(...$vars): void
    {
        foreach ($vars as $var) {
            /** @noinspection DebugFunctionUsageInspection */
            highlight_string(\sprintf("\n<?php\n\$var = %s;\n?>\n", var_export($var, true)));
        }
    }
}
