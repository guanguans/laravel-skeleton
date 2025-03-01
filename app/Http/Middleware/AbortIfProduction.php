<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\App;

class AbortIfProduction extends AbortIf
{
    #[\Override]
    protected function condition(): bool
    {
        return App::isProduction();
    }

    #[\Override]
    protected function code(): int
    {
        return 403;
    }
}
