<?php

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
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

        if ($events === []) {
            return null;
        }

        return array_values(array_unique($events));
    }
}
