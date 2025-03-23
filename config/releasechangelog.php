<?php

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

return [

    /**
     * Path where the changelog file exists or should create
     */
    'path' => resource_path(),
    'version_path' => resource_path(),

    /**
     * Type of version mode change
     */
    // 'version_mode' => 'increment', # or 'increment',

    /**
     * Version default format
     */
    'version_format_default' => 'version {major}.{minor}.{patch}.{prerelease}{prereleasenumber} [{buildmetadata}] (timestamp {timestamp})',

    'version_formats' => [
        'full' => '{major}.{minor}.{patch} [{buildmetadata}] {timestamp}',
        'changelogversion' => '{major}.{minor}.{patch}.{prerelease}{prereleasenumber}',
        'min' => '{major}.{minor}.{patch}[{prerelease}{prereleasenumber}]',
        'version' => '{major}.{minor}.{patch}',
        'minor' => '{minor}',
        'major' => '{major}',
        'patch' => '{patch}',
        'buildmetadata' => '{buildmetadata}',
        'commit' => '{commit}',
        'prerelease' => '{prerelease}{prereleasenumber}',
    ],

    /**
     * Is prerelease active
     */
    'prerelease' => true,

    'blade-directive' => 'releasechangelog',

    'markdown-path' => base_path(),

    'markdown-view-path' => '.'.DIRECTORY_SEPARATOR,

];
