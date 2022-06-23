<?php

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\App as Laravel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

if (! function_exists('validate')) {
    /**
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     *
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    function validate(array $data = [], array $rules = [], array $messages = [], array $customAttributes = [])
    {
        return validator($data, $rules, $messages, $customAttributes)->validate();
    }
}

if (! function_exists('array_filter_filled')) {
    /**
     * @param  array  $array
     *
     * @return array
     */
    function array_filter_filled(array $array)
    {
        return array_filter($array, function ($item) {
            return filled($item);
        });
    }
}

if (! function_exists('call')) {
    /**
     * Call the given Closure / class@method and inject its dependencies.
     *
     * @param  callable|string  $callback
     * @param  array  $parameters
     * @param  string|null  $defaultMethod
     * @return mixed
     */
    function call($callback, array $parameters = [], $defaultMethod = null)
    {
        app()->call($callback, $parameters, $defaultMethod);
    }
}

if (! function_exists('stopwatch')) {
    /**
     * @param  string  $name
     * @param  callable|string $callback
     * @param  array  $parameters
     * @param  null|string  $category
     * @param  bool  $morePrecision
     * @param  null|\Psr\Log\LoggerInterface  $logger
     *
     * @return mixed
     */
    function stopwatch(string $name, $callback, array $parameters = [], string $category = null, bool $morePrecision = false, LoggerInterface $logger = null)
    {
        $stopwatch = new Stopwatch($morePrecision);

        $logger or $logger = Laravel::make(LoggerInterface::class);

        $stopwatch->start($name, $category) and $logger->info("Start stopwatch [$name]");

        $called = Laravel::call($callback, $parameters);

        $stopwatchEvent = $stopwatch->stop($name) and $logger->info("End stopwatch [$name] [$stopwatchEvent]");

        return $called;
    }
}

if (! function_exists('wrap_query_log')) {
    /**
     * @param callable|string $callback
     * @param ...$parameter
     *
     * @return array
     */
    function wrap_query_log($callback, ...$parameter)
    {
        return (new Pipeline())
            ->send($callback)
            ->through(function ($callback, $next) {
                DB::enableQueryLog();
                DB::flushQueryLog();

                return $next($callback);
            })
            ->then(function ($callback) use ($parameter) {
                Laravel::call($callback, $parameter);

                return DB::getQueryLog();
            });
    }
}

if (! function_exists('dump_to_array')) {
    function dump_to_array(...$vars)
    {
        foreach ($vars as $var) {
            ($var instanceof Arrayable or method_exists($var, 'toArray')) ? dump($var->toArray()) : dump($var);
        }
    }
}

if (! function_exists('dd_to_array')) {
    function dd_to_array(...$vars)
    {
        dump_to_array(...$vars);
        exit(1);
    }
}

if (! function_exists('array_reduces')) {
    /**
     * @param  array  $array
     * @param  callable  $callback
     * @param  null  $carry
     *
     * @return null|mixed
     */
    function array_reduces(array $array, callable $callback, $carry = null)
    {
        foreach ($array as $key => $value) {
            $carry = call_user_func($callback, $carry, $value, $key);
        }

        return $carry;
    }
}

if (! function_exists('array_maps')) {
    /**
     * @param  callable  $callback
     * @param  array  $array
     *
     * @return array
     */
    function array_maps(callable $callback, array $array)
    {
        $arr = [];
        foreach ($array as $key => $value) {
            $arr[$key] = call_user_func($callback, $value, $key);
        }

        return $arr;
    }
}
