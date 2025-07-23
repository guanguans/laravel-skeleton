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

namespace App\Support\Attributes;

use App\Support\Managers\ElasticsearchManager;
use Elastic\Elasticsearch\Client;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Container\ContextualAttribute;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
final readonly class Elasticsearch implements ContextualAttribute
{
    public function __construct(public ?string $connection = null) {}

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function resolve(self $attribute, Container $container): Client
    {
        return $container->make(ElasticsearchManager::class)->connection($attribute->connection);
    }
}
