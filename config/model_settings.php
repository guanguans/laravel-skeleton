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

return [
    'settings_field_name' => env('MODEL_SETTINGS_FIELD_NAME', 'settings'),
    'settings_table_name' => env('MODEL_SETTINGS_TABLE_NAME', 'model_settings'),
    'settings_persistent' => env('MODEL_SETTINGS_PERSISTENT', true),
    'settings_table_use_cache' => env('MODEL_SETTINGS_TABLE_USE_CACHE', true),
    'settings_table_cache_prefix' => env('MODEL_SETTINGS_TABLE_CACHE_PREFIX', 'model_settings:'),
    'defaultSettings' => [
        'users' => [
        ],
    ],
];
