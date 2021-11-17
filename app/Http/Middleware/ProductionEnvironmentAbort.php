<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\App;

class ProductionEnvironmentAbort extends AbortIf
{
    protected function condition(): bool
    {
        return App::isProduction();
    }
}
