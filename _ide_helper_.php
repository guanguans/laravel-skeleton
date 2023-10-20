<?php

/** @noinspection AutoloadingIssuesInspection */
/** @noinspection FileClassnameCaseInspection */
/** @noinspection PhpComposerExtensionStubsInspection */

declare(strict_types=1);

namespace Illuminate\Support\Facades {
    /**
     * @mixin \Predis\Client
     * @mixin \Redis
     */
    class Redis extends Facade {}
}

namespace App\Support\Facades {
}
