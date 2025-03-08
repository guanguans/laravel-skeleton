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

use Illuminate\Database\Schema\Blueprint;
use Laragear\TwoFactor\Models\TwoFactorAuthentication;

return TwoFactorAuthentication::migration()->with(static function (Blueprint $table): void {
    // Here you can add custom columns to the Two Factor table.
    //
    // $table->string('alias')->nullable();
});
