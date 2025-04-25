<?php

/** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

/**
 * @see https://github.com/dasundev/dasun.dev/blob/main/app/Console/Commands/GenerateSitemap.php
 */
final class GenerateSitemapCommand extends Command
{
    protected $signature = 'generate:sitemap';
    protected $description = 'Generate the sitemap';

    public function handle(): void
    {
        SitemapGenerator::create(config('app.url'))
            ->getSitemap()
            // ->add(Url::create('/docs'))
            ->writeToFile(public_path('sitemap.xml'));
    }
}
