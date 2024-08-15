<?php

/** @noinspection All */

declare(strict_types=1);

/**
 * This file is part of the guanguans/laravel-skeleton.
 *
 * (c) guanguans <ityaozm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Illuminate\Support\Facades {
    // /**
    //  * @mixin \Predis\Client
    //  * @mixin \Redis
    //  */
    // class Redis extends Facade {}
}

namespace App\Support\Facades {
}



namespace Illuminate\Support {
    /**
     * @method $this inAppTimezone()
     * @method $this inUserTimezone(?string $guard = null)
     */
    class Carbon {}
}

namespace Illuminate\Contracts\Routing {
    /**
     * @mixin  \Illuminate\Routing\UrlGenerator
     */
    class UrlGenerator {}
}

