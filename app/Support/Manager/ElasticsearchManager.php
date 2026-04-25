<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2026 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Support\Manager;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;

/**
 * @mixin  \Elastic\Elasticsearch\Client
 */
final class ElasticsearchManager extends Manager
{
    use Conditionable;
    use Tappable;

    #[\Override]
    public function getDefaultDriver(): string
    {
        return $this->config->get('services.elasticsearch.default');
    }

    public function connection(?string $connection = null): Client
    {
        return $this->driver($connection);
    }

    /**
     * @throws \Elastic\Elasticsearch\Exception\ConfigException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Throwable
     *
     * @noinspection PhpMissingParentCallCommonInspection
     * @noinspection MissingParentCallInspection
     */
    #[\Override]
    protected function createDriver(mixed $driver): Client
    {
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        }

        $driverKey = "services.elasticsearch.connections.$driver";
        throw_unless($this->config->has($driverKey), \InvalidArgumentException::class, "Connection [$driver] not supported.");
        $config = $this->prepareConfig($this->config->get($driverKey));
        $method = \sprintf('create%sDriver', Str::studly($driver));

        if (method_exists($this, $method)) {
            return $this->{$method}($config);
        }

        $quiet = (bool) Arr::pull($config, 'quiet');

        return ClientBuilder::fromConfig($config, $quiet);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    private function prepareConfig(array $config): array
    {
        $default = ['quiet' => false];
        $main = Arr::except($this->config->get('services.elasticsearch', []), ['default', 'connections']);
        $config = array_replace_recursive($default, $main, $config);

        if (isset($config['hosts'])) {
            $config['hosts'] = array_filter(
                $config['hosts'],
                fn (array|string $host, int|string $indexOrEnv): bool => $this->container->environment(match (true) {
                    \is_string($indexOrEnv) => $indexOrEnv,
                    \is_array($host) and isset($host['env']) => $host['env'],
                    default => $this->container->environment(),
                }),
                \ARRAY_FILTER_USE_BOTH
            );
        }

        if (isset($config['logger'])) {
            $logger = $config['logger'];

            $config['logger'] = \is_string($logger) && $this->config->has("logging.channels.$logger")
                ? Log::channel($logger)
                : make($logger);
        }

        return $config;
    }
}
