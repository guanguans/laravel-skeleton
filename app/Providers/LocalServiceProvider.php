<?php

namespace App\Providers;

use Guanguans\LaravelSoar\SoarServiceProvider;
use Illuminate\Support\AggregateServiceProvider;

class LocalServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        SoarServiceProvider::class,
    ];
}
