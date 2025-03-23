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

use Essa\APIToolKit\Enum\GeneratorFilesType;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Generators
    |--------------------------------------------------------------------------
    |
    | The default options that will be created if no option is specified.
    |
    | Supported options: 'seeder', 'controller', 'request', 'resource', 'factory',
    |                    'migration', 'filter', 'test', 'routes'
    |
    */
    'default_generates' => [
        'seeder',
        'controller',
        'request',
        'resource',
        'factory',
        'migration',
        'filter',
        'test',
        'routes',
    ],
    /*
    |--------------------------------------------------------------------------
    | Default Generators
    |--------------------------------------------------------------------------
    | Number of items per page when using dynamic pagination.
    */
    'default_pagination_number' => 20,

    /*
    |--------------------------------------------------------------------------
    | Default Datetime Format for API Resources
    |--------------------------------------------------------------------------
    | The default format for displaying date and time values in API resources.
    | Used by the dateTimeFormat function when generating API resource responses,
    | ensuring consistent formatting for datetime values.
    */
    'datetime_format' => 'Y-m-d H:i:s',

    /*
    |--------------------------------------------------------------------------
    | Default Group
    |--------------------------------------------------------------------------
    |
    | Define the default generator group that will be used by default when
    | no specific group is specified. Users can still create and use custom
    | groups in addition to this default group.
    |
    */
    'default_group' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Custom Groups Files Paths
    |--------------------------------------------------------------------------
    |
    | Define file paths for different file types within custom groups.
    | Users can assign classes to each generator type for both the 'default' and
    | custom groups, specifying where the generated files will be stored.
    |
    */
    'groups_files_paths' => [
        'default' => [
            GeneratorFilesType::MODEL => [
                'folder_path' => app_path('Models'),
                'file_name' => '{ModelName}.php',
                'namespace' => 'App\Models',
            ],
            GeneratorFilesType::FACTORY => [
                'folder_path' => database_path('factories'),
                'file_name' => '{ModelName}Factory.php',
                'namespace' => 'Database\Factories',
            ],
            GeneratorFilesType::SEEDER => [
                'folder_path' => database_path('seeders'),
                'file_name' => '{ModelName}Seeder.php',
                'namespace' => 'Database\Seeders',
            ],
            GeneratorFilesType::CONTROLLER => [
                'folder_path' => app_path('Http/Controllers/API'),
                'file_name' => '{ModelName}Controller.php',
                'namespace' => 'App\Http\Controllers\API',
            ],
            GeneratorFilesType::RESOURCE => [
                'folder_path' => app_path('Http/Resources/{ModelName}'),
                'file_name' => '{ModelName}Resource.php',
                'namespace' => 'App\\Http\\Resources\\{ModelName}',
            ],
            GeneratorFilesType::TEST => [
                'folder_path' => base_path('tests/Feature'),
                'file_name' => '{ModelName}Test.php',
                'namespace' => 'Tests\Feature',
            ],
            GeneratorFilesType::CREATE_REQUEST => [
                'folder_path' => app_path('Http/Requests/{ModelName}'),
                'file_name' => 'Create{ModelName}Request.php',
                'namespace' => 'App\\Http\\Requests\\{ModelName}',
            ],
            GeneratorFilesType::UPDATE_REQUEST => [
                'folder_path' => app_path('Http/Requests/{ModelName}'),
                'file_name' => 'Update{ModelName}Request.php',
                'namespace' => 'App\\Http\\Requests\\{ModelName}',
            ],
            GeneratorFilesType::FILTER => [
                'folder_path' => app_path('Filters'),
                'file_name' => '{ModelName}Filters.php',
                'namespace' => 'App\Filters',
            ],
            GeneratorFilesType::MIGRATION => [
                'folder_path' => database_path('migrations'),
                'file_name' => Carbon\Carbon::now()
                    ->format('Y_m_d_His').'_create_{TableName}_table.php',
                'namespace' => null,
            ],
            GeneratorFilesType::ROUTES => [
                'folder_path' => base_path('routes'),
                'file_name' => 'api.php',
                'namespace' => null,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Groups Base URL Prefixes
    |--------------------------------------------------------------------------
    |
    | Define the base URLs for different groups in your application.
    | These base URLs are used as prefixes for routes defined within each
    | group.
    |
    */
    'groups_url_prefixes' => [
        'default' => '/api',
    ],
];
