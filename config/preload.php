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
    /*
    |--------------------------------------------------------------------------
    | Main Switch
    |--------------------------------------------------------------------------
    |
    | Preload detects if this environment is production and automatically runs
    | when under it. You can forcefully disable or enable it. Preload doesn't
    | run if your application is running test suites or any console command.
    |
    | Supported: "null", "true", "false".
    |
    */

    'enabled' => env('PRELOAD_ENABLE'),

    /*
    |--------------------------------------------------------------------------
    | Condition options
    |--------------------------------------------------------------------------
    |
    | Preload includes a convenient "condition" which generates a preload
    | script each 10,000 requests. You can change the options to pass to
    | the condition closure, or use your own closure and options array.
    |
    */

    'condition' => [
        'store' => null,
        'hits' => 10000,
        'key' => 'preload|request_count',
    ],

    /*
    |--------------------------------------------------------------------------
    | Root directory
    |--------------------------------------------------------------------------
    |
    | Some servers may share the same PHP main process, which may include files
    | outside the scope of this project. Setting this to "true" filters all
    | the files reported by Opcache to those inside this project path.
    |
    */

    'project_only' => true,

    /*
    |--------------------------------------------------------------------------
    | Memory Limit
    |--------------------------------------------------------------------------
    |
    | The Preloader script can be configured to handle a limited number of
    | files based on their memory consumption. The default is a safe bet
    | for most apps, but you can change it for your app specifically.
    |
    | Measured in MB (MegaBytes). Using `0` or `null` disables the limit.
    |
    */

    'memory' => 32,

    /*
    |--------------------------------------------------------------------------
    | Job configuration
    |--------------------------------------------------------------------------
    |
    | When the job is dispatched to store the script after the list is created,
    | it will use the defaults connection and queue. For most applications it
    | will be just fine, but you may want to change these here if you want.
    |
    */

    'job' => [
        'connection' => env('PRELOAD_JOB_CONNECTION'),
        'queue' => env('PRELOAD_JOB_QUEUE'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Output
    |--------------------------------------------------------------------------
    |
    | Once the Preload script is generated, it will be written to the root
    | path of your application, since it should have permission to write.
    | You can change the script output for anything as long is writable.
    |
    */

    'path' => base_path('preload.php'),

    /*
    |--------------------------------------------------------------------------
    | Upload method
    |--------------------------------------------------------------------------
    |
    | Opcache supports preloading files by using `require_once` (which executes
    | and resolves each file link), and `opcache_compile_file` (which not). If
    | you want to use require ensure the Composer Autoloader path is correct.
    |
    | You should set "use_require" to false unless you need scripts to be
    | executed to be resolved.
    |
    */

    'use_require' => false,
    'autoload' => base_path('vendor/autoload.php'),

    /*
    |--------------------------------------------------------------------------
    | Ignore Not Found
    |--------------------------------------------------------------------------
    |
    | Sometimes Opcache will include in the list files that are generated by
    | Laravel at runtime which don't exist when deploying the application.
    | To avoid errors on preloads, we can tell Preloader to ignore them.
    |
    */

    'ignore_not_found' => true,
];
