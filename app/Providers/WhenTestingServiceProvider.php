<?php

namespace App\Providers;

use Illuminate\Support\AggregateServiceProvider;

class WhenTestingServiceProvider extends AggregateServiceProvider
{
    /**
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    protected $providers = [
        // \Guanguans\LaravelSoar\SoarServiceProvider::class,
    ];
}
