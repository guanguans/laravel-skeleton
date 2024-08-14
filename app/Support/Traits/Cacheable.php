<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace App\Support\Traits;

use Carbon\Carbon;
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
     */
    protected static CacheManager $cache;

    /**
     * Flush the cache after create/update/delete events.
     */
    protected bool $eventFlushCache = false;

    /**
     * Global lifetime of the cache.
     */
    protected int $cacheMinutes = 60;

    /**
     * Set cache manager.
     */
    public static function setCacheInstance(CacheManager $cache): void
    {
        self::$cache = $cache;
    }

    /**
     * Get cache manager.
     */
    public static function getCacheInstance(): CacheManager
    {
        if (! self::$cache instanceof CacheManager) {
            self::$cache = app('cache');
        }

        return self::$cache;
    }

    /**
     * Determine if the cache will be skipped
     */
    public function skippedCache(): bool
    {
        return false === config('repositories.cache_enabled', false)
               || true === app('request')->has(config('repositories.cache_skip_param', 'skipCache'));
    }

    /**
     * Get Cache key for the method
     */
    public function getCacheKey(string $method, ?array $args = null, string $tag = ''): string
    {
        // Sort through arguments
        foreach ((array) $args as &$a) {
            if ($a instanceof Model) {
                $a = $a::class.'|'.$a->getKey();
            }
        }

        unset($a);

        // Create hash from arguments and query
        $args = serialize($args).serialize($this->getScopeQuery());

        return \sprintf(
            '%s-%s@%s-%s',
            config('app.locale'),
            $tag,
            $method,
            md5($args)
        );
    }

    /**
     * Get an item from the cache, or store the default value.
     */
    public function cacheCallback(string $method, array $args, \Closure $callback, null|int|string $time = null): mixed
    {
        // Cache disabled, just execute query & return result
        if (true === $this->skippedCache()) {
            return $callback();
        }

        // Use the called class name as the tag
        $tag = static::class;

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
        if (false === $this->eventFlushCache || false === config('repositories.cache_enabled', false)) {
            return false;
        }

        // Use the called class name as the tag
        $tag = static::class;

        return self::getCacheInstance()->tags(['repositories', $tag])->flush();
    }

    /**
     * Return the time until expires in minutes.
     */
    protected function getCacheExpiresTime(null|int|string $time = null): int
    {
        if (self::EXPIRES_END_OF_DAY === $time) {
            return class_exists(Carbon::class)
                ? round(Carbon::now()->secondsUntilEndOfDay() / 60)
                : $this->cacheMinutes;
        }

        return $time ?: $this->cacheMinutes;
    }
}
