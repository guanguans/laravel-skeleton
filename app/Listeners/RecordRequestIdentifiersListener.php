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

use Laravel\Sanctum\Events\TokenAuthenticated;

/**
 * @see https://github.com/nandi95/laravel-starter/blob/main/app/Listeners/RecordRequestIdentifiers.php
 */
final class RecordRequestIdentifiersListener
{
    public function handle(#[\SensitiveParameter] TokenAuthenticated $tokenAuthenticated): void {}
}
