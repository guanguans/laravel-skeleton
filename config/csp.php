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
     * Presets will determine which CSP headers will be set. A valid CSP preset is
     * any class that extends `Spatie\Csp\Preset`
     */
    'presets' => [
        Spatie\Csp\Presets\Basic::class,
    ],

    /**
     * Register additional global CSP directives here.
     */
    'directives' => [
        // Directive::SCRIPT => [Keyword::UNSAFE_EVAL, Keyword::UNSAFE_INLINE],
    ],

    /*
     * These presets which will be put in a report-only policy. This is great for testing out
     * a new policy or changes to existing CSP policy without breaking anything.
     */
    'report_only_presets' => [
    ],

    /**
     * Register additional global report-only CSP directives here.
     */
    'report_only_directives' => [
        // Directive::SCRIPT => [Keyword::UNSAFE_EVAL, Keyword::UNSAFE_INLINE],
    ],

    /*
     * All violations against a policy will be reported to this url.
     * A great service you could use for this is https://report-uri.com/
     */
    'report_uri' => env('CSP_REPORT_URI', ''),

    /*
     * Headers will only be added if this setting is set to true.
     */
    'enabled' => env('CSP_ENABLED', true),

    /*
     * The class responsible for generating the nonces used in inline tags and headers.
     */
    'nonce_generator' => Spatie\Csp\Nonce\RandomString::class,

    /*
     * Set false to disable automatic nonce generation and handling.
     * This is useful when you want to use 'unsafe-inline' for scripts/styles
     * and cannot add inline nonces.
     * Note that this will make your CSP policy less secure.
     */
    'nonce_enabled' => env('CSP_NONCE_ENABLED', true),
];
