<?php

namespace App\Providers;

use Illuminate\Support\AggregateServiceProvider;

class WhenLocalServiceProvider extends AggregateServiceProvider
{
    /**
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    protected $providers = [
        \Guanguans\LaravelSoar\SoarServiceProvider::class,
    ];
}
