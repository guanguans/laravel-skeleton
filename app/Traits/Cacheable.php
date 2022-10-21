<?php

namespace App\Traits;

use Carbon\Carbon;
use Closure;
use Illuminate\Cache\CacheManager;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \App\Support\AbstractRepository
 *
 * @see https://github.com/Torann/laravel-repository
 */
trait Cacheable
{
    /**
     * Cache instance
     *
     * @var CacheManager
     */
    protected static $cache;

    /**
     * Flush the cache after create/update/delete events.
     *
     * @var bool
     */
    protected $eventFlushCache = false;

    /**
     * Global lifetime of the cache.
     *
     * @var int
     */
    protected $cacheMinutes = 60;

    /**
     * Set cache manager.
     */
    public static function setCacheInstance(CacheManager $cache)
    {
        self::$cache = $cache;
    }

    /**
     * Get cache manager.
     */
    public static function getCacheInstance(): CacheManager
    {
        if (self::$cache === null) {
            self::$cache = app('cache');
        }

        return self::$cache;
    }

    /**
     * Determine if the cache will be skipped
     */
    public function skippedCache(): bool
    {
        return config('repositories.cache_enabled', false) === false
               || app('request')->has(config('repositories.cache_skip_param', 'skipCache')) === true;
    }

    /**
     * Get Cache key for the method
     *
     * @param ?array  $args
     */
    public function getCacheKey(string $method, ?array $args = null, string $tag = ''): string
    {
        // Sort through arguments
        foreach ((array)$args as &$a) {
            if ($a instanceof Model) {
                $a = get_class($a) . '|' . $a->getKey();
            }
        }

        unset($a);

        // Create hash from arguments and query
        $args = serialize($args) . serialize($this->getScopeQuery());

        return sprintf(
            '%s-%s@%s-%s',
            config('app.locale'),
            $tag,
            $method,
            md5($args)
        );
    }

    /**
     * Get an item from the cache, or store the default value.
     *
     * @param int|string|null $time
     *
     * @return mixed
     */
    public function cacheCallback(string $method, array $args, Closure $callback, $time = null)
    {
        // Cache disabled, just execute query & return result
        if ($this->skippedCache() === true) {
            return call_user_func($callback);
        }

        // Use the called class name as the tag
        $tag = get_called_class();

        return self::getCacheInstance()->tags(['repositories', $tag])->remember(
            $this->getCacheKey($method, $args, $tag),
            $this->getCacheExpiresTime($time),
            $callback
        );
    }

    /**
     * Flush the cache for the given repository.
     */
    public function flushCache(): bool
    {
        // Cache disabled, just ignore this
        if ($this->eventFlushCache === false || config('repositories.cache_enabled', false) === false) {
            return false;
        }

        // Use the called class name as the tag
        $tag = get_called_class();

        return self::getCacheInstance()->tags(['repositories', $tag])->flush();
    }

    /**
     * Return the time until expires in minutes.
     *
     * @param int|string|null $time
     */
    protected function getCacheExpiresTime($time = null): int
    {
        if ($time === self::EXPIRES_END_OF_DAY) {
            return class_exists(Carbon::class)
                ? round(Carbon::now()->secondsUntilEndOfDay() / 60)
                : $this->cacheMinutes;
        }

        return $time ?: $this->cacheMinutes;
    }
}
