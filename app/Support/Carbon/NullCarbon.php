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

namespace App\Support\Carbon;

use Illuminate\Support\Carbon;

/**
 * @see https://github.com/mikebronner/laravel-null-carbon
 */
class NullCarbon extends Carbon implements \Stringable
{
    public function __toString()
    {
        return '';
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function format(string $format): string
    {
        return '';
    }

    public function jsonSerialize(): mixed
    {
        return json_encode(null);
    }
}
