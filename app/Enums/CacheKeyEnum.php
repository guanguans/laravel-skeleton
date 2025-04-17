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

namespace App\Enums;

/**
 * @see https://github.com/anisAronno/laravel-starter/blob/develop/app/Helpers/CacheKey.php
 */
enum CacheKeyEnum: string
{
    case ROLE = 'role_';
    case USER = 'user_';
    case PRODUCT = 'product_';
    case CATEGORY = 'category_';
}
