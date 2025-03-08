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

namespace App\Listeners;

use Illuminate\Http\Request;
use Laravel\Sanctum\Events\TokenAuthenticated;
use Stevebauman\Location\Drivers\Cloudflare;
use Stevebauman\Location\Facades\Location;
use Stevebauman\Location\LocationManager;

/**
 * @see https://github.com/nandi95/laravel-starter/blob/main/app/Listeners/RecordRequestIdentifiers.php
 */
class RecordRequestIdentifiersListener
{
    /**
     * Handle the event.
     */
    public function handle(TokenAuthenticated $event): void
    {
        /** @var null|Request $request */
        $request = \Illuminate\Support\Facades\Request::getFacadeRoot();

        if (null !== $request && ($request->ip() !== $event->token->ip || null === $event->token->location)) {
            $attributes = [
                'ip' => $request->ip(),
            ];

            // https://github.com/stevebauman/location/blob/master/src/Drivers/Cloudflare.php#L17
            /** @var LocationManager $location */
            /** @phpstan-ignore-next-line */
            $location = Location::setDriver(new Cloudflare);
            // https://developers.cloudflare.com/rules/transform/managed-transforms/reference/#add-visitor-location-headers
            $ipLocation = $location->get($request->ip());

            if ($ipLocation) {
                $attributes['location'] = $ipLocation->cityName;
            }

            $event->token->forceFill($attributes);

            app()->terminating(static fn () => $event->token->save());
        }
    }
}
