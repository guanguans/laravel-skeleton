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
     * @see \Illuminate\Log\LogManager::resolve()
     *
     * @throws \Elastic\Elasticsearch\Exception\ConfigException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Throwable
     */
    #[\Override]
    protected function createDriver(mixed $driver): Client
    {
        try {
            return parent::createDriver($driver);
        } catch (\InvalidArgumentException $invalidArgumentException) {
            if ($invalidArgumentException->getMessage() !== "Driver [$driver] not supported.") {
                throw $invalidArgumentException;
            }

            $config = $this->configurationFor($driver);
            $quiet = (bool) Arr::pull($config, 'quiet');

            return ClientBuilder::fromConfig($config, $quiet);
        }
    }

    /**
     * @see \Illuminate\Log\LogManager::resolve()
     * @see \Illuminate\Log\LogManager::configurationFor()
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Throwable
     */
    private function configurationFor(string $driver): array
    {
        $configKey = "services.elasticsearch.connections.$driver";

        if (!$this->config->has($configKey)) {
            throw new \InvalidArgumentException("Connection [$driver] is not defined.");
        }

        return $this->prepareConfig($this->config->array($configKey));
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
        $defaultConfig = ['quiet' => false];
        $mainConfig = Arr::except($this->config->get('services.elasticsearch', []), ['default', 'connections']);
        // $config = array_replace($defaultConfig, $mainConfig, $config);
        $config += ($mainConfig + $defaultConfig);

        if (isset($config['hosts'])) {
            $config['hosts'] = array_filter(
                $config['hosts'],
                fn (string $host, int|string $indexOrEnv): bool => $this->container->environment(
                    \is_string($indexOrEnv) ? $indexOrEnv : $this->container->environment()
                ),
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
