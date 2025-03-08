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

namespace App\Models\Concerns;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait Observable
{
    public static function bootObservable(): void
    {
        $events = (new static)->getObservableEvents();

        $traitEvents = self::collectEventsRegisteredByTraits();

        if (null !== $traitEvents) {
            $events = array_values(array_unique(array_merge($events, $traitEvents)));
        }

        $class = static::class;

        foreach ($events as $event) {
            $method = 'on'.ucfirst($event);

            if (method_exists($class, $method)) {
                static::registerModelEvent($event, $class.'@'.$method);
            }
        }
    }

    private static function collectEventsRegisteredByTraits(): ?array
    {
        $class = static::class;
        $events = [];

        foreach (class_uses_recursive($class) as $trait) {
            $method = 'register'.class_basename($trait).'Events';

            if (method_exists($class, $method)) {
                foreach (static::{$method}() as $event) {
                    $events[] = $event;
                }
            }
        }

        if ([] === $events) {
            return null;
        }

        return array_values(array_unique($events));
    }
}
