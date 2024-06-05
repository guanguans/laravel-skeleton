<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\App;

class AbortIfProduction extends AbortIf
{
    protected function condition(): bool
    {
        return App::isProduction();
    }

    protected function code(): int
    {
        return 403;
    }
}
