<?php

/** @noinspection All */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
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
